# macOS Monitoring in EyeNet

## Overview
EyeNet provides comprehensive monitoring capabilities for macOS systems, including standard Unix-like metrics and macOS-specific features.

## Monitored Metrics

### Standard Metrics
- CPU Usage and Load Averages
- Memory Usage (including virtual memory pages)
- Disk Usage and I/O Statistics
- Network Interface Statistics
- System Uptime
- Process Information

### macOS-Specific Metrics

#### Power Management
- Battery Level and Health
- Charge Cycles
- Power Source Status
- Battery Temperature

#### Thermal Metrics
- CPU Die Temperature
- Fan Speeds
- Thermal Pressure Level

#### System Security
- FileVault Status
- System Integrity Protection (SIP) Status
- Gatekeeper Status
- XProtect Version

#### Performance Metrics
- Disk I/O Operations
- Virtual Memory Statistics
- App Memory Pressure
- System Memory Pressure

#### Software Updates
- System Updates Available
- App Store Updates Pending

## Default Thresholds

### CPU
- Warning: 70% utilization
- Critical: 85% utilization

### Memory
- Warning: 80% usage
- Critical: 90% usage
- Swap Warning: 50% usage
- Swap Critical: 75% usage

### Disk
- Warning: 85% usage
- Critical: 95% usage
- I/O Warning: 1000 ops/sec
- I/O Critical: 2000 ops/sec

### Power (Laptops)
- Battery Warning: 30% remaining
- Battery Critical: 10% remaining
- Temperature Warning: 80°C
- Temperature Critical: 95°C

## Installation Requirements

1. XAMPP for macOS
2. PHP 7.4 or higher
3. Required PHP Extensions:
   - curl
   - json
   - pdo
   - sockets

## Permissions

The monitoring agent requires certain permissions to collect all metrics:

1. Basic metrics (no special permissions needed):
   - CPU usage
   - Memory usage
   - Disk space
   - Network statistics

2. Advanced metrics (requires sudo access):
   - Power management data
   - Thermal sensors
   - Security status

## Configuration

### Agent Configuration
```php
// config.php
return [
    'collect_interval' => 300,  // 5 minutes
    'metrics' => [
        'standard' => true,     // CPU, Memory, Disk, Network
        'power' => true,        // Battery and power management
        'thermal' => true,      // Temperature and fans
        'security' => true,     // FileVault, SIP, Gatekeeper
        'updates' => true       // System and App Store updates
    ],
    'thresholds' => [
        // Custom thresholds (optional)
        // Will use defaults if not specified
    ]
];
```

## Troubleshooting

### Common Issues

1. Missing Metrics
   - Check agent permissions
   - Verify required tools are installed
   - Check system log for errors

2. High Resource Usage
   - Adjust collection interval
   - Disable unnecessary metrics
   - Check for stuck processes

3. Security Alerts
   - Verify FileVault status
   - Check SIP configuration
   - Update security preferences

## Best Practices

1. Security
   - Enable FileVault
   - Keep SIP enabled
   - Maintain Gatekeeper settings

2. Performance
   - Monitor Memory Pressure
   - Track Disk I/O
   - Watch for thermal throttling

3. Maintenance
   - Regular system updates
   - Monitor battery health
   - Check security status
