<?php

require_once __DIR__ . '/config/config.php';

class EyeNetAgent {
    private $apiKey;
    private $serverUrl;
    private $config;
    
    public function __construct($config) {
        $this->apiKey = $config['api_key'];
        $this->serverUrl = $config['server_url'];
        $this->config = $config;
    }
    
    public function collectMetrics() {
        $metrics = [
            'timestamp' => time(),
            'hostname' => gethostname(),
            'system' => [
                'os' => php_uname('s'),
                'version' => php_uname('r'),
                'machine' => php_uname('m')
            ],
            'resources' => $this->getSystemResources(),
            'network' => $this->getNetworkStats(),
            'processes' => $this->getProcessInfo()
        ];
        
        return $metrics;
    }
    
    private function getSystemResources() {
        $resources = [];
        
        // Memory info
        if (is_readable('/proc/meminfo')) {
            $meminfo = file_get_contents('/proc/meminfo');
            preg_match_all('/^(\w+):\s+(\d+)/m', $meminfo, $matches);
            $meminfo = array_combine($matches[1], $matches[2]);
            
            $resources['memory'] = [
                'total' => isset($meminfo['MemTotal']) ? $meminfo['MemTotal'] * 1024 : 0,
                'free' => isset($meminfo['MemFree']) ? $meminfo['MemFree'] * 1024 : 0,
                'available' => isset($meminfo['MemAvailable']) ? $meminfo['MemAvailable'] * 1024 : 0,
                'cached' => isset($meminfo['Cached']) ? $meminfo['Cached'] * 1024 : 0
            ];
        } else {
            // macOS memory info
            $cmd = "vm_stat | perl -ne '/page size of (\d+)/ and \$size=\$1; /Pages\s+([^:]+)[^\\d]+(\d+)/ and printf(\"\$1:\$2\\n\");'";
            exec($cmd, $output);
            $meminfo = [];
            foreach ($output as $line) {
                list($key, $value) = explode(':', $line);
                $meminfo[$key] = $value;
            }
            
            $pageSize = 4096; // Default page size
            $resources['memory'] = [
                'total' => $pageSize * ($meminfo['free'] + $meminfo['active'] + $meminfo['inactive'] + $meminfo['speculative'] + $meminfo['wired down']),
                'free' => $pageSize * $meminfo['free'],
                'active' => $pageSize * $meminfo['active'],
                'inactive' => $pageSize * $meminfo['inactive']
            ];
        }
        
        // CPU info
        if (is_readable('/proc/stat')) {
            $cpu = file_get_contents('/proc/stat');
            preg_match('/^cpu\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)/m', $cpu, $matches);
            $total = $matches[1] + $matches[2] + $matches[3] + $matches[4];
            $idle = $matches[4];
            $resources['cpu'] = [
                'usage' => round((1 - ($idle / $total)) * 100, 2)
            ];
        } else {
            // macOS CPU info
            $cmd = "top -l 1 | grep -E '^CPU'";
            exec($cmd, $output);
            if (!empty($output)) {
                preg_match('/([0-9.]+)% idle/', $output[0], $matches);
                $idle = isset($matches[1]) ? $matches[1] : 0;
                $resources['cpu'] = [
                    'usage' => round(100 - $idle, 2)
                ];
            }
        }
        
        // Disk info
        $cmd = "df -k | grep -vE '^Filesystem|tmpfs|cdrom'";
        exec($cmd, $output);
        $resources['disk'] = [];
        foreach ($output as $line) {
            $parts = preg_split('/\s+/', $line);
            if (count($parts) >= 6) {
                $resources['disk'][] = [
                    'filesystem' => $parts[0],
                    'size' => $parts[1] * 1024,
                    'used' => $parts[2] * 1024,
                    'available' => $parts[3] * 1024,
                    'usage_percent' => rtrim($parts[4], '%')
                ];
            }
        }
        
        return $resources;
    }
    
    private function getNetworkStats() {
        $stats = [];
        
        if (PHP_OS === 'Darwin') {
            // macOS network stats
            $cmd = "netstat -ib | grep -v 'Name'";
            exec($cmd, $output);
            foreach ($output as $line) {
                $parts = preg_split('/\s+/', trim($line));
                if (count($parts) >= 7) {
                    $interface = $parts[0];
                    if ($interface !== 'lo0') { // Skip loopback
                        $stats[$interface] = [
                            'rx_bytes' => $parts[6],
                            'tx_bytes' => $parts[9],
                            'rx_packets' => $parts[4],
                            'tx_packets' => $parts[7],
                            'errors' => $parts[5] + $parts[8]
                        ];
                    }
                }
            }
        } else {
            // Linux network stats
            if (is_readable('/proc/net/dev')) {
                $lines = file('/proc/net/dev');
                foreach ($lines as $line) {
                    if (preg_match('/:/', $line)) {
                        $parts = preg_split('/[:\s]+/', trim($line));
                        $interface = $parts[0];
                        if ($interface !== 'lo') { // Skip loopback
                            $stats[$interface] = [
                                'rx_bytes' => $parts[1],
                                'rx_packets' => $parts[2],
                                'rx_errors' => $parts[3],
                                'tx_bytes' => $parts[9],
                                'tx_packets' => $parts[10],
                                'tx_errors' => $parts[11]
                            ];
                        }
                    }
                }
            }
        }
        
        return $stats;
    }
    
    private function getProcessInfo() {
        $processes = [];
        
        if (PHP_OS === 'Darwin') {
            // macOS process info
            $cmd = "ps -eo pid,ppid,user,%cpu,%mem,state,command";
        } else {
            // Linux process info
            $cmd = "ps -eo pid,ppid,user,%cpu,%mem,state,cmd";
        }
        
        exec($cmd, $output);
        array_shift($output); // Remove header
        
        foreach ($output as $line) {
            $parts = preg_split('/\s+/', trim($line), 7);
            if (count($parts) >= 7) {
                $processes[] = [
                    'pid' => $parts[0],
                    'ppid' => $parts[1],
                    'user' => $parts[2],
                    'cpu_percent' => $parts[3],
                    'mem_percent' => $parts[4],
                    'state' => $parts[5],
                    'command' => $parts[6]
                ];
            }
        }
        
        return $processes;
    }
    
    public function sendMetrics($metrics) {
        $url = rtrim($this->serverUrl, '/') . '/api/metrics';
        $data = json_encode([
            'api_key' => $this->apiKey,
            'metrics' => $metrics
        ]);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ]);
        
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'response' => $result,
            'http_code' => $httpCode
        ];
    }
    
    public function testConnection() {
        $url = rtrim($this->serverUrl, '/') . '/api/test';
        $data = json_encode(['api_key' => $this->apiKey]);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ]);
        
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'response' => $result,
            'http_code' => $httpCode
        ];
    }
}

// Handle command line arguments
$options = getopt('', ['test']);

// Load configuration
$config = require __DIR__ . '/config/config.php';

// Create agent instance
$agent = new EyeNetAgent($config);

// Test mode
if (isset($options['test'])) {
    $result = $agent->testConnection();
    echo "Connection test result:\n";
    echo "HTTP Code: " . $result['http_code'] . "\n";
    echo "Response: " . $result['response'] . "\n";
    exit($result['success'] ? 0 : 1);
}

// Normal operation
$metrics = $agent->collectMetrics();
$result = $agent->sendMetrics($metrics);

if (!$result['success']) {
    error_log("Failed to send metrics: " . $result['response']);
    exit(1);
}

exit(0);
