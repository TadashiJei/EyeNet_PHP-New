<?php

class NetworkReport {
    private $db;
    private $timeframe;
    
    public function __construct($db, $timeframe = '24h') {
        $this->db = $db;
        $this->timeframe = $timeframe;
    }
    
    /**
     * Generate a detailed network traffic report
     */
    public function generateTrafficReport($serverId) {
        $timeLimit = $this->getTimeLimit();
        
        $sql = "SELECT metrics, collected_at 
                FROM server_metrics 
                WHERE server_id = ? AND collected_at >= ?
                ORDER BY collected_at ASC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$serverId, $timeLimit]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $report = [
            'summary' => $this->calculateSummary($data),
            'traffic_patterns' => $this->analyzeTrafficPatterns($data),
            'interface_stats' => $this->getInterfaceStats($data),
            'anomalies' => $this->detectTrafficAnomalies($data),
            'bandwidth_usage' => $this->calculateBandwidthUsage($data)
        ];
        
        return $report;
    }
    
    /**
     * Calculate overall network statistics
     */
    private function calculateSummary($data) {
        $summary = [
            'total_rx_bytes' => 0,
            'total_tx_bytes' => 0,
            'peak_rx_rate' => 0,
            'peak_tx_rate' => 0,
            'avg_rx_rate' => 0,
            'avg_tx_rate' => 0
        ];
        
        foreach ($data as $row) {
            $metrics = json_decode($row['metrics'], true);
            if (isset($metrics['network'])) {
                foreach ($metrics['network'] as $interface) {
                    $summary['total_rx_bytes'] += $interface['rx_bytes'];
                    $summary['total_tx_bytes'] += $interface['tx_bytes'];
                    
                    $summary['peak_rx_rate'] = max($summary['peak_rx_rate'], $interface['rx_bytes']);
                    $summary['peak_tx_rate'] = max($summary['peak_tx_rate'], $interface['tx_bytes']);
                }
            }
        }
        
        if (count($data) > 0) {
            $summary['avg_rx_rate'] = $summary['total_rx_bytes'] / count($data);
            $summary['avg_tx_rate'] = $summary['total_tx_bytes'] / count($data);
        }
        
        return $summary;
    }
    
    /**
     * Analyze traffic patterns throughout the day
     */
    private function analyzeTrafficPatterns($data) {
        $patterns = [
            'hourly' => array_fill(0, 24, ['rx' => 0, 'tx' => 0, 'count' => 0]),
            'daily' => array_fill(0, 7, ['rx' => 0, 'tx' => 0, 'count' => 0])
        ];
        
        foreach ($data as $row) {
            $metrics = json_decode($row['metrics'], true);
            $timestamp = strtotime($row['collected_at']);
            $hour = (int)date('G', $timestamp);
            $day = (int)date('w', $timestamp);
            
            if (isset($metrics['network'])) {
                foreach ($metrics['network'] as $interface) {
                    $patterns['hourly'][$hour]['rx'] += $interface['rx_bytes'];
                    $patterns['hourly'][$hour]['tx'] += $interface['tx_bytes'];
                    $patterns['hourly'][$hour]['count']++;
                    
                    $patterns['daily'][$day]['rx'] += $interface['rx_bytes'];
                    $patterns['daily'][$day]['tx'] += $interface['tx_bytes'];
                    $patterns['daily'][$day]['count']++;
                }
            }
        }
        
        // Calculate averages
        foreach ($patterns as &$type) {
            foreach ($type as &$period) {
                if ($period['count'] > 0) {
                    $period['rx_avg'] = $period['rx'] / $period['count'];
                    $period['tx_avg'] = $period['tx'] / $period['count'];
                }
            }
        }
        
        return $patterns;
    }
    
    /**
     * Get detailed interface statistics
     */
    private function getInterfaceStats($data) {
        $interfaces = [];
        
        foreach ($data as $row) {
            $metrics = json_decode($row['metrics'], true);
            if (isset($metrics['network'])) {
                foreach ($metrics['network'] as $name => $interface) {
                    if (!isset($interfaces[$name])) {
                        $interfaces[$name] = [
                            'rx_total' => 0,
                            'tx_total' => 0,
                            'rx_packets' => 0,
                            'tx_packets' => 0,
                            'errors' => 0,
                            'status' => 'active'
                        ];
                    }
                    
                    $interfaces[$name]['rx_total'] += $interface['rx_bytes'];
                    $interfaces[$name]['tx_total'] += $interface['tx_bytes'];
                    $interfaces[$name]['rx_packets'] += $interface['rx_packets'];
                    $interfaces[$name]['tx_packets'] += $interface['tx_packets'];
                }
            }
        }
        
        return $interfaces;
    }
    
    /**
     * Detect traffic anomalies using statistical analysis
     */
    private function detectTrafficAnomalies($data) {
        $anomalies = [];
        $rxValues = [];
        $txValues = [];
        
        // Collect all values
        foreach ($data as $row) {
            $metrics = json_decode($row['metrics'], true);
            if (isset($metrics['network'])) {
                foreach ($metrics['network'] as $interface) {
                    $rxValues[] = $interface['rx_bytes'];
                    $txValues[] = $interface['tx_bytes'];
                }
            }
        }
        
        // Calculate statistics
        $rxMean = array_sum($rxValues) / count($rxValues);
        $txMean = array_sum($txValues) / count($txValues);
        
        $rxStdDev = $this->calculateStdDev($rxValues, $rxMean);
        $txStdDev = $this->calculateStdDev($txValues, $txMean);
        
        // Detect anomalies (values > 2 standard deviations from mean)
        foreach ($data as $row) {
            $metrics = json_decode($row['metrics'], true);
            if (isset($metrics['network'])) {
                foreach ($metrics['network'] as $name => $interface) {
                    if (abs($interface['rx_bytes'] - $rxMean) > 2 * $rxStdDev) {
                        $anomalies[] = [
                            'timestamp' => $row['collected_at'],
                            'interface' => $name,
                            'type' => 'rx_traffic',
                            'value' => $interface['rx_bytes'],
                            'threshold' => $rxMean + 2 * $rxStdDev
                        ];
                    }
                    
                    if (abs($interface['tx_bytes'] - $txMean) > 2 * $txStdDev) {
                        $anomalies[] = [
                            'timestamp' => $row['collected_at'],
                            'interface' => $name,
                            'type' => 'tx_traffic',
                            'value' => $interface['tx_bytes'],
                            'threshold' => $txMean + 2 * $txStdDev
                        ];
                    }
                }
            }
        }
        
        return $anomalies;
    }
    
    /**
     * Calculate bandwidth usage percentiles
     */
    private function calculateBandwidthUsage($data) {
        $rxValues = [];
        $txValues = [];
        
        foreach ($data as $row) {
            $metrics = json_decode($row['metrics'], true);
            if (isset($metrics['network'])) {
                foreach ($metrics['network'] as $interface) {
                    $rxValues[] = $interface['rx_bytes'];
                    $txValues[] = $interface['tx_bytes'];
                }
            }
        }
        
        sort($rxValues);
        sort($txValues);
        
        $count = count($rxValues);
        
        return [
            'rx' => [
                'p50' => $rxValues[(int)($count * 0.5)],
                'p90' => $rxValues[(int)($count * 0.9)],
                'p95' => $rxValues[(int)($count * 0.95)],
                'p99' => $rxValues[(int)($count * 0.99)]
            ],
            'tx' => [
                'p50' => $txValues[(int)($count * 0.5)],
                'p90' => $txValues[(int)($count * 0.9)],
                'p95' => $txValues[(int)($count * 0.95)],
                'p99' => $txValues[(int)($count * 0.99)]
            ]
        ];
    }
    
    /**
     * Calculate standard deviation
     */
    private function calculateStdDev($values, $mean) {
        $variance = 0;
        foreach ($values as $value) {
            $variance += pow($value - $mean, 2);
        }
        $variance /= count($values);
        return sqrt($variance);
    }
    
    /**
     * Get time limit based on timeframe
     */
    private function getTimeLimit() {
        $now = time();
        switch ($this->timeframe) {
            case '1h':
                return date('Y-m-d H:i:s', $now - 3600);
            case '6h':
                return date('Y-m-d H:i:s', $now - 21600);
            case '12h':
                return date('Y-m-d H:i:s', $now - 43200);
            case '7d':
                return date('Y-m-d H:i:s', $now - 604800);
            case '30d':
                return date('Y-m-d H:i:s', $now - 2592000);
            case '24h':
            default:
                return date('Y-m-d H:i:s', $now - 86400);
        }
    }
}
