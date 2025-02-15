<?php
/**
 * EyeNet macOS Monitoring Agent
 * Collects and reports system metrics for macOS systems
 */

// Configuration
$config = parse_ini_file(__DIR__ . '/macos-agent.conf');
$server_id = $config['SERVER_ID'];
$api_key = $config['API_KEY'];
$api_url = $config['API_URL'];

// Collect System Metrics
function collectMetrics() {
    $metrics = [];
    
    // Basic System Info
    $metrics['os'] = trim(shell_exec('sw_vers -productName') . ' ' . shell_exec('sw_vers -productVersion'));
    $metrics['hostname'] = gethostname();
    $metrics['uptime'] = trim(shell_exec('uptime | cut -d "," -f1'));
    
    // CPU Usage
    $cpu_usage = shell_exec("ps -A -o %cpu | awk '{s+=$1} END {print s}'");
    $metrics['cpu'] = round(floatval($cpu_usage), 2);
    
    // Memory Usage
    $memory = shell_exec("vm_stat | perl -ne '/page size of (\d+)/ and \$size=\$1; /Pages\s+([^:]+)[^\\d]+(\d+)/ and \$sum+=\$2*\$size; END { print \$sum/1024/1024 }'");
    $total_memory = shell_exec("sysctl -n hw.memsize | awk '{print \$0/1024/1024}'");
    $metrics['memory'] = [
        'total' => round(floatval($total_memory)),
        'used' => round(floatval($memory)),
        'free' => round(floatval($total_memory) - floatval($memory))
    ];
    
    // Disk Usage
    $disk_info = shell_exec("df -h / | tail -n 1");
    preg_match_all('/\s+(\d+)%/', $disk_info, $matches);
    $metrics['disk'] = [
        'usage_percent' => isset($matches[1][0]) ? intval($matches[1][0]) : 0
    ];
    
    // Network Stats
    $netstat = shell_exec("netstat -ib | grep -e 'en0' -e 'en1' | head -n 1");
    preg_match_all('/\s+(\d+)\s+(\d+)/', $netstat, $matches);
    $metrics['network'] = [
        'bytes_in' => isset($matches[1][0]) ? intval($matches[1][0]) : 0,
        'bytes_out' => isset($matches[2][0]) ? intval($matches[2][0]) : 0
    ];
    
    // Battery Information (if available)
    $battery_info = shell_exec("pmset -g batt");
    if (preg_match('/(\d+)%/', $battery_info, $matches)) {
        $metrics['power'] = [
            'battery_percentage' => intval($matches[1]),
            'battery_health' => strpos($battery_info, 'health: good') !== false ? 'Good' : 'Check Battery'
        ];
    }
    
    // CPU Temperature (requires additional permissions)
    $temp_cmd = shell_exec("sudo powermetrics --samplers smc -n 1 2>&1");
    if (preg_match('/CPU die temperature: (\d+\.\d+)/', $temp_cmd, $matches)) {
        $metrics['thermal'] = [
            'cpu_temperature' => round(floatval($matches[1]), 1)
        ];
    }
    
    // Security Status
    $filevault = shell_exec("fdesetup status");
    $sip = shell_exec("csrutil status");
    $metrics['security'] = [
        'filevault_enabled' => strpos($filevault, 'FileVault is On') !== false,
        'sip_enabled' => strpos($sip, 'enabled') !== false
    ];
    
    // Software Updates
    $updates = shell_exec("softwareupdate -l");
    $metrics['updates'] = [
        'updates_available' => strpos($updates, 'No new software available') === false,
        'last_check' => date('Y-m-d H:i:s')
    ];
    
    return $metrics;
}

// Send Metrics to Server
function sendMetrics($metrics) {
    global $api_url, $server_id, $api_key;
    
    $data = [
        'server_id' => $server_id,
        'api_key' => $api_key,
        'data' => json_encode($metrics)
    ];
    
    $ch = curl_init($api_url . 'agent/update.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;
}

// Main Loop
while (true) {
    try {
        $metrics = collectMetrics();
        sendMetrics($metrics);
    } catch (Exception $e) {
        error_log("Error in macOS agent: " . $e->getMessage());
    }
    
    // Wait for 60 seconds before next update
    sleep(60);
}
