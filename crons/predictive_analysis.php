<?php

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../EyeNet-Architecture/models/MLIntegration.php';

/**
 * Predictive Analysis Cron Job
 * This script should be run every 5 minutes via crontab
 */

try {
    // Initialize database connection
    $db = new PDO(
        "mysql:host={$config['db_host']};dbname={$config['db_name']}", 
        $config['db_user'], 
        $config['db_pass']
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Initialize ML Integration
    $mlIntegration = new MLIntegration([
        'db' => $db,
        'timeout' => 60
    ]);

    // Get list of monitored servers
    $stmt = $db->query("SELECT * FROM app_servers WHERE status = 1");
    $servers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($servers as $server) {
        try {
            // Collect current metrics
            $metrics = $mlIntegration->collectServerMetrics(
                $server['hostname'],
                [
                    'username' => $server['ssh_username'],
                    'password' => $server['ssh_password'] // Note: Consider using SSH keys instead
                ]
            );

            // Get predictions
            $predictions = $mlIntegration->getPredictions($server['hostname']);

            // Check for critical predictions
            $alerts = [];
            
            // CPU prediction check
            if (isset($predictions['cpu']['predicted']) && $predictions['cpu']['predicted'] > 90) {
                $alerts[] = [
                    'type' => 'cpu_warning',
                    'message' => "High CPU usage predicted ({$predictions['cpu']['predicted']}%) for server {$server['hostname']}",
                    'severity' => 'warning'
                ];
            }

            // Memory prediction check
            if (isset($predictions['memory']['predicted']) && $predictions['memory']['predicted'] > 90) {
                $alerts[] = [
                    'type' => 'memory_warning',
                    'message' => "High memory usage predicted ({$predictions['memory']['predicted']}%) for server {$server['hostname']}",
                    'severity' => 'warning'
                ];
            }

            // Disk space prediction check
            if (isset($predictions['disk']['predicted']) && $predictions['disk']['predicted'] > 90) {
                $alerts[] = [
                    'type' => 'disk_warning',
                    'message' => "High disk usage predicted ({$predictions['disk']['predicted']}%) for server {$server['hostname']}",
                    'severity' => 'warning'
                ];
            }

            // Check for anomalies
            if (!empty($predictions['anomalies'])) {
                foreach ($predictions['anomalies'] as $metric => $anomaly) {
                    if ($anomaly['severity'] === 'high') {
                        $alerts[] = [
                            'type' => "{$metric}_anomaly",
                            'message' => "Anomaly detected in {$metric} metrics for server {$server['hostname']}",
                            'severity' => 'critical'
                        ];
                    }
                }
            }

            // Store alerts in database
            if (!empty($alerts)) {
                $stmt = $db->prepare("
                    INSERT INTO app_servers_alerts 
                    (serverid, type, message, severity, created_at) 
                    VALUES (?, ?, ?, ?, NOW())
                ");

                foreach ($alerts as $alert) {
                    $stmt->execute([
                        $server['id'],
                        $alert['type'],
                        $alert['message'],
                        $alert['severity']
                    ]);
                }
            }

            // Store predictions for historical analysis
            $stmt = $db->prepare("
                INSERT INTO server_predictions 
                (server_id, predictions, created_at) 
                VALUES (?, ?, NOW())
            ");
            $stmt->execute([$server['id'], json_encode($predictions)]);

        } catch (Exception $e) {
            // Log server-specific errors but continue with other servers
            error_log("Error processing server {$server['hostname']}: " . $e->getMessage());
            continue;
        }
    }

} catch (Exception $e) {
    error_log("Predictive analysis cron error: " . $e->getMessage());
    exit(1);
}
