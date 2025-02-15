<?php
require_once 'EyeNet-Architecture/models/MLIntegration.php';

// Initialize the ML Integration with test configuration
$config = [
    'endpoint' => 'http://localhost:5000',
    'timeout' => 30,
    'db' => null // We'll use mock data for testing
];

$ml = new MLIntegration($config);

// Test server credentials
$credentials = [
    'username' => 'test_user',
    'password' => 'test_password'
];

try {
    // 1. Test metric collection
    echo "Testing server metric collection...\n";
    $metrics = $ml->collectServerMetrics('localhost', $credentials);
    echo "Current server metrics:\n";
    echo json_encode($metrics, JSON_PRETTY_PRINT) . "\n\n";

    // 2. Test predictions
    echo "Testing predictive analysis...\n";
    $predictions = $ml->getPredictions('localhost');
    echo "Predictions:\n";
    echo json_encode($predictions, JSON_PRETTY_PRINT) . "\n\n";

    // 3. Test anomaly detection
    echo "Testing anomaly detection...\n";
    if (!empty($predictions['anomalies'])) {
        echo "Detected anomalies:\n";
        foreach ($predictions['anomalies'] as $metric => $anomaly) {
            echo "- {$metric}: Current value {$anomaly['value']}, Threshold {$anomaly['threshold']}, Severity: {$anomaly['severity']}\n";
        }
    } else {
        echo "No anomalies detected.\n";
    }
    echo "\n";

    // 4. Test model metrics
    echo "Testing model performance metrics...\n";
    $metrics = $ml->getMetrics();
    echo "Model metrics:\n";
    echo json_encode($metrics, JSON_PRETTY_PRINT) . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
