<?php

class MacOSMonitor {
    private static $defaultThresholds = [
        'cpu' => [
            'warning' => 70,    // macOS tends to be more CPU efficient
            'critical' => 85
        ],
        'memory' => [
            'warning' => 80,    // macOS memory management is different from Linux
            'critical' => 90,
            'swap_warning' => 50,
            'swap_critical' => 75
        ],
        'disk' => [
            'warning' => 85,
            'critical' => 95,
            'iops_warning' => 1000,
            'iops_critical' => 2000
        ],
        'power' => [
            'battery_warning' => 30,
            'battery_critical' => 10,
            'temperature_warning' => 80,  // CPU temperature in Celsius
            'temperature_critical' => 95
        ]
    ];

    public static function getSpecificMetrics($data) {
        $metrics = [];
        
        // Power management metrics (specific to macOS laptops)
        $powerInfo = self::getPowerInfo();
        if ($powerInfo) {
            $metrics['power'] = $powerInfo;
        }
        
        // Thermal metrics
        $metrics['thermal'] = self::getThermalMetrics();
        
        // App Store and System Updates
        $metrics['updates'] = self::getUpdateStatus();
        
        // Security & Privacy
        $metrics['security'] = self::getSecurityStatus();
        
        // Performance metrics
        $metrics['performance'] = self::getPerformanceMetrics();
        
        return $metrics;
    }

    private static function getPowerInfo() {
        $power = [];
        exec("system_profiler SPPowerDataType 2>/dev/null", $output);
        
        foreach ($output as $line) {
            if (strpos($line, 'Charge Remaining') !== false) {
                preg_match('/(\d+)%/', $line, $matches);
                $power['battery_percentage'] = isset($matches[1]) ? (int)$matches[1] : null;
            }
            if (strpos($line, 'Cycle Count') !== false) {
                preg_match('/Cycle Count: (\d+)/', $line, $matches);
                $power['battery_cycles'] = isset($matches[1]) ? (int)$matches[1] : null;
            }
            if (strpos($line, 'Condition') !== false) {
                $power['battery_health'] = trim(str_replace('Condition:', '', $line));
            }
        }
        
        return $power;
    }

    private static function getThermalMetrics() {
        $thermal = [];
        
        // Get CPU temperature using SMC
        exec("sudo powermetrics --samplers smc -i1 -n1 2>/dev/null", $output);
        foreach ($output as $line) {
            if (strpos($line, 'CPU die temperature') !== false) {
                preg_match('/[\d.]+/', $line, $matches);
                $thermal['cpu_temperature'] = isset($matches[0]) ? (float)$matches[0] : null;
            }
        }
        
        return $thermal;
    }

    private static function getUpdateStatus() {
        $updates = [];
        
        // Check for system updates
        exec("softwareupdate -l 2>/dev/null", $output);
        $updates['system_updates_available'] = count(array_filter($output, function($line) {
            return strpos($line, 'Label:') !== false;
        }));
        
        // Check for App Store updates
        exec("mas outdated 2>/dev/null", $output);
        $updates['app_store_updates'] = count($output);
        
        return $updates;
    }

    private static function getSecurityStatus() {
        $security = [];
        
        // Check FileVault status
        exec("fdesetup status 2>/dev/null", $output);
        $security['filevault_enabled'] = strpos(implode("\n", $output), 'FileVault is On') !== false;
        
        // Check SIP status
        exec("csrutil status 2>/dev/null", $output);
        $security['sip_enabled'] = strpos(implode("\n", $output), 'enabled') !== false;
        
        // Check Gatekeeper status
        exec("spctl --status 2>/dev/null", $output);
        $security['gatekeeper_enabled'] = strpos(implode("\n", $output), 'enabled') !== false;
        
        return $security;
    }

    private static function getPerformanceMetrics() {
        $performance = [];
        
        // Get I/O stats using iostat
        exec("iostat -d 1 2 2>/dev/null | tail -n 1", $output);
        if (!empty($output)) {
            $stats = preg_split('/\s+/', trim($output[0]));
            if (count($stats) >= 6) {
                $performance['disk_operations'] = [
                    'reads_per_sec' => (float)$stats[3],
                    'writes_per_sec' => (float)$stats[4],
                    'kb_per_transfer' => (float)$stats[5]
                ];
            }
        }
        
        // Get virtual memory stats
        exec("vm_stat 2>/dev/null", $output);
        foreach ($output as $line) {
            if (strpos($line, 'Pages free') !== false) {
                preg_match('/:\s+(\d+)/', $line, $matches);
                $performance['vm_free_pages'] = isset($matches[1]) ? (int)$matches[1] : 0;
            }
            if (strpos($line, 'Pages active') !== false) {
                preg_match('/:\s+(\d+)/', $line, $matches);
                $performance['vm_active_pages'] = isset($matches[1]) ? (int)$matches[1] : 0;
            }
        }
        
        return $performance;
    }

    public static function getThresholds() {
        return self::$defaultThresholds;
    }

    public static function validateMetrics($metrics) {
        $alerts = [];
        $thresholds = self::$defaultThresholds;
        
        // CPU Temperature Check
        if (isset($metrics['thermal']['cpu_temperature'])) {
            if ($metrics['thermal']['cpu_temperature'] >= $thresholds['power']['temperature_critical']) {
                $alerts[] = [
                    'type' => 'critical',
                    'component' => 'cpu_temperature',
                    'message' => 'CPU temperature is critically high: ' . $metrics['thermal']['cpu_temperature'] . '°C'
                ];
            } elseif ($metrics['thermal']['cpu_temperature'] >= $thresholds['power']['temperature_warning']) {
                $alerts[] = [
                    'type' => 'warning',
                    'component' => 'cpu_temperature',
                    'message' => 'CPU temperature is high: ' . $metrics['thermal']['cpu_temperature'] . '°C'
                ];
            }
        }
        
        // Battery Health Check
        if (isset($metrics['power']['battery_percentage'])) {
            if ($metrics['power']['battery_percentage'] <= $thresholds['power']['battery_critical']) {
                $alerts[] = [
                    'type' => 'critical',
                    'component' => 'battery',
                    'message' => 'Battery level is critically low: ' . $metrics['power']['battery_percentage'] . '%'
                ];
            } elseif ($metrics['power']['battery_percentage'] <= $thresholds['power']['battery_warning']) {
                $alerts[] = [
                    'type' => 'warning',
                    'component' => 'battery',
                    'message' => 'Battery level is low: ' . $metrics['power']['battery_percentage'] . '%'
                ];
            }
        }
        
        // Security Checks
        if (isset($metrics['security'])) {
            if (!$metrics['security']['filevault_enabled']) {
                $alerts[] = [
                    'type' => 'warning',
                    'component' => 'security',
                    'message' => 'FileVault is not enabled'
                ];
            }
            if (!$metrics['security']['sip_enabled']) {
                $alerts[] = [
                    'type' => 'warning',
                    'component' => 'security',
                    'message' => 'System Integrity Protection (SIP) is disabled'
                ];
            }
        }
        
        return $alerts;
    }
}
