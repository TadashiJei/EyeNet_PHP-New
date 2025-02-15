<?php

class MLIntegration {
    private $modelEndpoint;
    private $apiKey;
    private $timeout;
    private $db;
    private $predictionCache = [];
    private $lastDataCollection = null;
    private $collectionInterval = 300; // 5 minutes

    public function __construct($config = []) {
        $this->modelEndpoint = $config['endpoint'] ?? 'http://localhost:5000';
        $this->apiKey = $config['api_key'] ?? null;
        $this->timeout = $config['timeout'] ?? 30;
        $this->db = $config['db'] ?? null;
        
        if (!extension_loaded('ssh2')) {
            throw new Exception('SSH2 extension is required for server monitoring');
        }
    }

    /**
     * Collect current server metrics
     * @param string $host Server hostname
     * @param array $credentials SSH credentials
     * @return array Server metrics
     */
    public function collectServerMetrics($host, $credentials) {
        $metrics = [];
        
        // Connect via SSH
        $connection = ssh2_connect($host, 22);
        if ($connection === false) {
            throw new Exception('Failed to connect to server');
        }
        
        if (!ssh2_auth_password($connection, $credentials['username'], $credentials['password'])) {
            throw new Exception('Authentication failed');
        }
        
        // Collect CPU usage
        $stream = ssh2_exec($connection, 'top -bn1 | grep "Cpu(s)"');
        stream_set_blocking($stream, true);
        $cpu_usage = stream_get_contents($stream);
        $metrics['cpu'] = $this->parseCpuUsage($cpu_usage);
        
        // Collect memory usage
        $stream = ssh2_exec($connection, 'free -m');
        stream_set_blocking($stream, true);
        $memory = stream_get_contents($stream);
        $metrics['memory'] = $this->parseMemoryUsage($memory);
        
        // Collect disk usage
        $stream = ssh2_exec($connection, 'df -h');
        stream_set_blocking($stream, true);
        $disk = stream_get_contents($stream);
        $metrics['disk'] = $this->parseDiskUsage($disk);
        
        // Collect network statistics
        $stream = ssh2_exec($connection, 'netstat -i');
        stream_set_blocking($stream, true);
        $network = stream_get_contents($stream);
        $metrics['network'] = $this->parseNetworkStats($network);
        
        // Store metrics in database
        $this->storeMetrics($host, $metrics);
        
        return $metrics;
    }

    /**
     * Parse CPU usage from top command
     */
    private function parseCpuUsage($data) {
        preg_match('/\d+\.\d+(?=\s*id)/', $data, $matches);
        return isset($matches[0]) ? 100 - floatval($matches[0]) : null;
    }

    /**
     * Parse memory usage from free command
     */
    private function parseMemoryUsage($data) {
        $lines = explode("\n", trim($data));
        $memory = [];
        
        foreach ($lines as $line) {
            if (strpos($line, 'Mem:') === 0) {
                $parts = preg_split('/\s+/', trim($line));
                $memory['total'] = intval($parts[1]);
                $memory['used'] = intval($parts[2]);
                $memory['free'] = intval($parts[3]);
                $memory['usage_percent'] = ($memory['used'] / $memory['total']) * 100;
            }
        }
        
        return $memory;
    }

    /**
     * Parse disk usage from df command
     */
    private function parseDiskUsage($data) {
        $lines = explode("\n", trim($data));
        $disks = [];
        
        foreach ($lines as $line) {
            if (strpos($line, '/dev/') === 0) {
                $parts = preg_split('/\s+/', trim($line));
                $disks[] = [
                    'filesystem' => $parts[0],
                    'size' => $parts[1],
                    'used' => $parts[2],
                    'available' => $parts[3],
                    'usage_percent' => intval(rtrim($parts[4], '%'))
                ];
            }
        }
        
        return $disks;
    }

    /**
     * Parse network statistics from netstat
     */
    private function parseNetworkStats($data) {
        $lines = explode("\n", trim($data));
        $interfaces = [];
        
        foreach ($lines as $line) {
            if (strpos($line, 'eth') === 0 || strpos($line, 'wlan') === 0) {
                $parts = preg_split('/\s+/', trim($line));
                $interfaces[$parts[0]] = [
                    'rx_packets' => intval($parts[3]),
                    'tx_packets' => intval($parts[7]),
                    'rx_bytes' => intval($parts[4]),
                    'tx_bytes' => intval($parts[8])
                ];
            }
        }
        
        return $interfaces;
    }

    /**
     * Store metrics in database
     */
    private function storeMetrics($host, $metrics) {
        if (!$this->db) return;
        
        $sql = "INSERT INTO server_metrics (host, metrics, collected_at) VALUES (?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$host, json_encode($metrics)]);
    }

    /**
     * Get predictions for server behavior
     * @param string $host Server hostname
     * @return array Prediction results
     */
    public function getPredictions($host) {
        // Check cache first
        if (isset($this->predictionCache[$host]) && 
            time() - $this->predictionCache[$host]['timestamp'] < 300) {
            return $this->predictionCache[$host]['data'];
        }
        
        // Get historical data
        $historicalData = $this->getHistoricalData($host);
        
        // Calculate trends and predictions
        $predictions = $this->analyzeTrends($historicalData);
        
        // Cache results
        $this->predictionCache[$host] = [
            'timestamp' => time(),
            'data' => $predictions
        ];
        
        return $predictions;
    }

    /**
     * Get historical server data
     */
    private function getHistoricalData($host) {
        if (!$this->db) return [];
        
        $sql = "SELECT metrics, collected_at FROM server_metrics 
                WHERE host = ? AND collected_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                ORDER BY collected_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$host]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Analyze trends and make predictions
     */
    private function analyzeTrends($historicalData) {
        $predictions = [
            'cpu' => $this->predictMetric('cpu', $historicalData),
            'memory' => $this->predictMetric('memory', $historicalData),
            'disk' => $this->predictMetric('disk', $historicalData),
            'network' => $this->predictMetric('network', $historicalData)
        ];
        
        // Add anomaly detection
        $predictions['anomalies'] = $this->detectAnomalies($historicalData);
        
        return $predictions;
    }

    /**
     * Predict specific metric using simple moving average
     */
    private function predictMetric($metric, $historicalData) {
        $values = array_map(function($record) use ($metric) {
            $metrics = json_decode($record['metrics'], true);
            return isset($metrics[$metric]) ? $metrics[$metric] : null;
        }, $historicalData);
        
        // Calculate moving average
        $windowSize = 12; // 1-hour window with 5-minute intervals
        $movingAvg = [];
        
        for ($i = $windowSize; $i < count($values); $i++) {
            $window = array_slice($values, $i - $windowSize, $windowSize);
            $movingAvg[] = array_sum($window) / count($window);
        }
        
        // Predict next value using linear regression
        if (count($movingAvg) >= 2) {
            $lastTwo = array_slice($movingAvg, -2);
            $slope = $lastTwo[1] - $lastTwo[0];
            $prediction = end($movingAvg) + $slope;
        } else {
            $prediction = end($values);
        }
        
        return [
            'current' => end($values),
            'predicted' => $prediction,
            'trend' => $this->calculateTrend($values)
        ];
    }

    /**
     * Calculate trend direction
     */
    private function calculateTrend($values) {
        $recentValues = array_slice($values, -6); // Last 30 minutes
        $firstAvg = array_sum(array_slice($recentValues, 0, 3)) / 3;
        $lastAvg = array_sum(array_slice($recentValues, -3)) / 3;
        
        if ($lastAvg > $firstAvg * 1.1) return 'increasing';
        if ($lastAvg < $firstAvg * 0.9) return 'decreasing';
        return 'stable';
    }

    /**
     * Detect anomalies in metrics
     */
    private function detectAnomalies($historicalData) {
        $anomalies = [];
        $metrics = ['cpu', 'memory', 'disk', 'network'];
        
        foreach ($metrics as $metric) {
            $values = array_map(function($record) use ($metric) {
                $metrics = json_decode($record['metrics'], true);
                return isset($metrics[$metric]) ? $metrics[$metric] : null;
            }, $historicalData);
            
            // Calculate mean and standard deviation
            $mean = array_sum($values) / count($values);
            $variance = array_sum(array_map(function($x) use ($mean) {
                return pow($x - $mean, 2);
            }, $values)) / count($values);
            $stdDev = sqrt($variance);
            
            // Check last value for anomaly
            $lastValue = end($values);
            if (abs($lastValue - $mean) > 2 * $stdDev) {
                $anomalies[$metric] = [
                    'value' => $lastValue,
                    'threshold' => $mean + 2 * $stdDev,
                    'severity' => abs($lastValue - $mean) > 3 * $stdDev ? 'high' : 'medium'
                ];
            }
        }
        
        return $anomalies;
    }
}

    /**
     * Get model performance metrics
     * @return array Performance metrics
     */
    public function getMetrics() {
        // TODO: Implement metrics retrieval
        return [
            'accuracy' => 0.95,
            'response_time' => '100ms',
            'predictions_made' => 1000
        ];
    }

    /**
     * Format data for ML model
     * @param array $rawData Raw network data
     * @return array Formatted data
     */
    private function formatData($rawData) {
        // TODO: Implement data formatting
        return $rawData;
    }

    /**
     * Validate model response
     * @param array $response Model response
     * @return bool Validation result
     */
    private function validateResponse($response) {
        // TODO: Implement response validation
        return true;
    }
}
