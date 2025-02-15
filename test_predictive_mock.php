<?php
require_once 'EyeNet-Architecture/models/MLIntegration.php';

class MockMLIntegration extends MLIntegration {
    public function __construct($config = []) {
        $this->modelEndpoint = $config['endpoint'] ?? 'http://localhost:5000';
        $this->apiKey = $config['api_key'] ?? null;
        $this->timeout = $config['timeout'] ?? 30;
        $this->db = $config['db'] ?? null;
    }

    // Override to use mock data
    public function collectServerMetrics($host, $credentials) {
        return [
            'cpu' => 45.2,
            'memory' => [
                'total' => 16384,
                'used' => 8192,
                'free' => 8192,
                'usage_percent' => 50.0
            ],
            'disk' => [
                [
                    'filesystem' => '/dev/sda1',
                    'size' => '256G',
                    'used' => '128G',
                    'available' => '128G',
                    'usage_percent' => 50
                ]
            ],
            'network' => [
                'eth0' => [
                    'rx_packets' => 1000000,
                    'tx_packets' => 900000,
                    'rx_bytes' => 1500000000,
                    'tx_bytes' => 1200000000
                ]
            ]
        ];
    }

    // Override to use mock historical data
    protected function getHistoricalData($host) {
        $data = [];
        $baseTime = time() - (24 * 3600); // Start 24 hours ago
        
        // Generate 288 data points (5-minute intervals for 24 hours)
        for ($i = 0; $i < 288; $i++) {
            // Create realistic patterns
            $hour = ($i * 5) / 60; // Convert to hours
            
            // CPU load pattern: Higher during work hours (8-18)
            $workHourLoad = ($hour >= 8 && $hour <= 18) ? 30 : 0;
            $cpuLoad = 20 + $workHourLoad + sin($hour/6) * 15 + rand(-5, 5);
            
            // Memory usage: Gradual increase during the day, reset after maintenance
            $memoryBase = 40 + ($hour % 24) * 0.5;
            $memoryUsage = min(95, $memoryBase + rand(-2, 2));
            
            // Disk usage: Steady increase
            $diskBase = 50 + ($i / 288) * 10;
            
            // Network traffic: Peaks during work hours
            $networkBase = ($hour >= 8 && $hour <= 18) ? 1500000000 : 500000000;
            $networkLoad = $networkBase + sin($hour/6) * 500000000 + rand(-100000000, 100000000);
            
            $metrics = [
                'cpu' => $cpuLoad,
                'memory' => [
                    'total' => 16384,
                    'used' => round($memoryUsage * 163.84),
                    'free' => round((100 - $memoryUsage) * 163.84),
                    'usage_percent' => $memoryUsage
                ],
                'disk' => [
                    [
                        'filesystem' => '/dev/sda1',
                        'size' => '256G',
                        'used' => round($diskBase) . 'G',
                        'available' => (256 - round($diskBase)) . 'G',
                        'usage_percent' => round($diskBase)
                    ]
                ],
                'network' => [
                    'eth0' => [
                        'rx_packets' => round($networkLoad / 1500),
                        'tx_packets' => round($networkLoad / 1800),
                        'rx_bytes' => round($networkLoad),
                        'tx_bytes' => round($networkLoad * 0.8)
                    ]
                ]
            ];

            $data[] = [
                'metrics' => json_encode($metrics),
                'collected_at' => date('Y-m-d H:i:s', $baseTime + ($i * 300))
            ];
        }

        return $data;
    }
}

// Initialize the Mock ML Integration
$config = [
    'endpoint' => 'http://localhost:5000',
    'timeout' => 30
];

$ml = new MockMLIntegration($config);

try {
    // 1. Test metric collection
    echo "Testing server metric collection...\n";
    $metrics = $ml->collectServerMetrics('localhost', []);
    echo "Current server metrics:\n";
    echo json_encode($metrics, JSON_PRETTY_PRINT) . "\n\n";

    // 2. Test predictions
    echo "Testing predictive analysis...\n";
    $predictions = $ml->getPredictions('localhost');
    echo "Predictions:\n";
    echo json_encode($predictions, JSON_PRETTY_PRINT) . "\n\n";

    // 3. Test model metrics
    echo "Testing model performance metrics...\n";
    $metrics = $ml->getMetrics();
    echo "Model metrics:\n";
    echo json_encode($metrics, JSON_PRETTY_PRINT) . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
