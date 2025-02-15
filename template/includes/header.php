<?php
// Get current page information
$currentPage = isset($_GET['route']) ? $_GET['route'] : 'dashboard';
$pageTitle = ucfirst($currentPage);

// Define SEO metadata based on current page
$seoTitles = [
    'dashboard' => 'EyeNet - Network Monitoring Dashboard with Predictive Analytics',
    'servers' => 'Server Monitoring & Management - EyeNet',
    'websites' => 'Website Performance Monitoring - EyeNet',
    'checks' => 'Network Health Checks & Alerts - EyeNet',
    'analytics' => 'Network Analytics & Insights - EyeNet'
];

$seoDescriptions = [
    'dashboard' => 'Real-time network monitoring dashboard with AI-powered predictive analytics. Monitor servers, websites, and network performance with intelligent alerts.',
    'servers' => 'Comprehensive server monitoring solution with predictive maintenance, performance tracking, and automated alerting system.',
    'websites' => 'Monitor website availability, performance, and user experience with advanced analytics and early warning system.',
    'checks' => 'Configure and manage network health checks with customizable alerts and predictive failure detection.',
    'analytics' => 'Advanced network analytics with machine learning insights, trend analysis, and performance optimization recommendations.'
];

// Get SEO data for current page
$pageTitle = $seoTitles[$currentPage] ?? 'EyeNet - Intelligent Network Monitoring';
$pageDescription = $seoDescriptions[$currentPage] ?? 'Enterprise-grade network monitoring system with predictive analytics and machine learning capabilities.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Meta Tags -->
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta name="keywords" content="network monitoring, predictive analytics, server monitoring, website monitoring, network management, AI-powered monitoring">
    
    <!-- Open Graph / Social Media Meta Tags -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta property="og:image" content="<?php echo $config['base_url']; ?>/template/assets/images/og-image.jpg">
    <meta property="og:url" content="<?php echo $config['base_url'] . $_SERVER['REQUEST_URI']; ?>">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta name="twitter:image" content="<?php echo $config['base_url']; ?>/template/assets/images/twitter-card.jpg">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo $config['base_url']; ?>/template/assets/images/favicon.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $config['base_url']; ?>/template/assets/images/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $config['base_url']; ?>/template/assets/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo $config['base_url']; ?>/template/assets/images/android-chrome-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="<?php echo $config['base_url']; ?>/template/assets/images/android-chrome-512x512.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $config['base_url']; ?>/template/assets/images/apple-touch-icon.png">
    <link rel="manifest" href="<?php echo $config['base_url']; ?>/template/assets/site.webmanifest">
    <meta name="theme-color" content="#2196F3">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="<?php echo $config['base_url'] . $_SERVER['REQUEST_URI']; ?>">
    
    <!-- Preload Critical Resources -->
    <link rel="preload" href="<?php echo $config['base_url']; ?>/template/assets/images/logo.svg" as="image" type="image/svg+xml">
    <link rel="preload" href="<?php echo $config['base_url']; ?>/template/assets/css/style.css" as="style">
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo $config['base_url']; ?>/template/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo $config['base_url']; ?>/template/assets/modern-override.css">
    
    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "http://schema.org",
        "@type": "SoftwareApplication",
        "name": "EyeNet",
        "applicationCategory": "NetworkMonitoringApplication",
        "operatingSystem": "Linux",
        "description": "<?php echo htmlspecialchars($pageDescription); ?>",
        "offers": {
            "@type": "Offer",
            "price": "0",
            "priceCurrency": "USD"
        }
    }
    </script>
</head>
