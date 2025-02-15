# EyeNet: Linux Server Setup Guide

## Table of Contents
1. [System Requirements](#system-requirements)
2. [Initial Server Setup](#initial-server-setup)
3. [Security Configuration](#security-configuration)
4. [Required Software Installation](#required-software-installation)
5. [Database Setup](#database-setup)
6. [PHP Configuration](#php-configuration)
7. [Web Server Configuration](#web-server-configuration)
8. [EyeNet Installation](#eyenet-installation)
9. [Monitoring Setup](#monitoring-setup)
10. [Troubleshooting](#troubleshooting)

## System Requirements

### Minimum Hardware Requirements
- CPU: 2 cores
- RAM: 4GB
- Storage: 20GB
- Network: 100Mbps

### Recommended Hardware Requirements
- CPU: 4+ cores
- RAM: 8GB or more
- Storage: 50GB or more
- Network: 1Gbps

### Supported Linux Distributions
- Ubuntu Server 20.04 LTS or newer
- CentOS 8 or newer
- Debian 11 or newer
- Red Hat Enterprise Linux 8 or newer

## Initial Server Setup

### 1. Update System Packages
```bash
# Ubuntu/Debian
sudo apt update
sudo apt upgrade -y

# CentOS/RHEL
sudo dnf update -y
```

### 2. Create Service User
```bash
# Create eyenet user
sudo useradd -m -s /bin/bash eyenet

# Add to necessary groups
sudo usermod -aG sudo eyenet
```

### 3. Configure Timezone
```bash
# Set timezone
sudo timedatectl set-timezone Asia/Singapore

# Install NTP
sudo apt install ntp -y
sudo systemctl enable ntp
sudo systemctl start ntp
```

## Security Configuration

### 1. SSH Security
```bash
# Edit SSH config
sudo nano /etc/ssh/sshd_config

# Recommended settings
Port 2222                    # Change default SSH port
PermitRootLogin no          # Disable root login
PasswordAuthentication no    # Use key-based authentication only
MaxAuthTries 3              # Limit authentication attempts

# Restart SSH service
sudo systemctl restart sshd
```

### 2. Firewall Configuration
```bash
# Ubuntu/Debian (UFW)
sudo ufw allow 2222/tcp     # SSH
sudo ufw allow 80/tcp       # HTTP
sudo ufw allow 443/tcp      # HTTPS
sudo ufw enable

# CentOS/RHEL (firewalld)
sudo firewall-cmd --permanent --add-port=2222/tcp
sudo firewall-cmd --permanent --add-port=80/tcp
sudo firewall-cmd --permanent --add-port=443/tcp
sudo firewall-cmd --reload
```

### 3. SELinux Configuration (CentOS/RHEL)
```bash
# Check SELinux status
sestatus

# Configure SELinux for web server
sudo setsebool -P httpd_can_network_connect 1
sudo setsebool -P httpd_can_network_connect_db 1
```

## Required Software Installation

### 1. Web Server (Apache)
```bash
# Ubuntu/Debian
sudo apt install apache2 -y

# CentOS/RHEL
sudo dnf install httpd -y
sudo systemctl enable httpd
sudo systemctl start httpd
```

### 2. PHP and Extensions
```bash
# Ubuntu/Debian
sudo apt install php8.1 php8.1-cli php8.1-common php8.1-mysql \
    php8.1-zip php8.1-gd php8.1-mbstring php8.1-curl php8.1-xml \
    php8.1-bcmath php8.1-ssh2 -y

# CentOS/RHEL
sudo dnf install php php-cli php-common php-mysql php-zip \
    php-gd php-mbstring php-curl php-xml php-bcmath php-ssh2 -y
```

### 3. MySQL/MariaDB
```bash
# Ubuntu/Debian
sudo apt install mariadb-server -y

# CentOS/RHEL
sudo dnf install mariadb-server -y
sudo systemctl enable mariadb
sudo systemctl start mariadb

# Secure installation
sudo mysql_secure_installation
```

### 4. Additional Tools
```bash
# Install required tools
sudo apt install git curl wget zip unzip net-tools htop -y
```

## Database Setup

### 1. Create Database and User
```bash
# Access MySQL
sudo mysql -u root -p

# Create database and user
CREATE DATABASE eyenet;
CREATE USER 'eyenet'@'localhost' IDENTIFIED BY 'your_strong_password';
GRANT ALL PRIVILEGES ON eyenet.* TO 'eyenet'@'localhost';
FLUSH PRIVILEGES;
```

### 2. Import Schema
```bash
# Navigate to SQL directory
cd /var/www/eyenet/install/sql

# Import base schema
mysql -u eyenet -p eyenet < db.sql

# Import predictive analysis schema
mysql -u eyenet -p eyenet < predictive_analysis.sql
```

## PHP Configuration

### 1. PHP INI Settings
```bash
# Edit php.ini
sudo nano /etc/php/8.1/apache2/php.ini

# Recommended settings
memory_limit = 256M
upload_max_filesize = 64M
post_max_size = 64M
max_execution_time = 300
date.timezone = Asia/Singapore
```

### 2. Install Composer
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

## Web Server Configuration

### 1. Apache Virtual Host
```bash
# Create virtual host file
sudo nano /etc/apache2/sites-available/eyenet.conf

# Configuration content
<VirtualHost *:80>
    ServerName eyenet.yourdomain.com
    DocumentRoot /var/www/eyenet
    
    <Directory /var/www/eyenet>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/eyenet_error.log
    CustomLog ${APACHE_LOG_DIR}/eyenet_access.log combined
</VirtualHost>

# Enable site
sudo a2ensite eyenet.conf
sudo systemctl restart apache2
```

### 2. SSL Configuration (Optional)
```bash
# Install Certbot
sudo apt install certbot python3-certbot-apache -y

# Obtain SSL certificate
sudo certbot --apache -d eyenet.yourdomain.com
```

## EyeNet Installation

### 1. Clone Repository
```bash
# Navigate to web root
cd /var/www

# Clone repository
sudo git clone https://github.com/your-repo/eyenet.git
sudo chown -R www-data:www-data eyenet
```

### 2. Install Dependencies
```bash
cd eyenet
composer install
```

### 3. Configure Environment
```bash
# Copy example config
cp config.example.php config.php

# Edit configuration
nano config.php
```

## Monitoring Setup

### 1. Configure Cron Jobs
```bash
# Edit crontab
sudo crontab -e

# Add cron jobs
*/5 * * * * php /var/www/eyenet/crons/predictive_analysis.php
*/1 * * * * php /var/www/eyenet/crons/server_check.php
0 0 * * * php /var/www/eyenet/crons/cleanup.php
```

### 2. Set Up Log Rotation
```bash
# Create log rotation config
sudo nano /etc/logrotate.d/eyenet

# Add configuration
/var/www/eyenet/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
}
```

## Troubleshooting

### Common Issues and Solutions

1. **Permission Issues**
```bash
# Fix directory permissions
sudo chown -R www-data:www-data /var/www/eyenet
sudo chmod -R 755 /var/www/eyenet
sudo chmod -R 775 /var/www/eyenet/storage
```

2. **PHP Extensions Missing**
```bash
# Check PHP modules
php -m

# Install missing extensions
sudo apt install php8.1-[extension_name]
```

3. **Database Connection Issues**
```bash
# Check MySQL service
sudo systemctl status mysql

# Check MySQL logs
sudo tail -f /var/log/mysql/error.log
```

4. **Web Server Issues**
```bash
# Check Apache status
sudo systemctl status apache2

# Check Apache logs
sudo tail -f /var/log/apache2/error.log
```

### Performance Tuning

1. **MySQL Optimization**
```bash
# Edit MySQL configuration
sudo nano /etc/mysql/my.cnf

# Recommended settings for 8GB RAM
innodb_buffer_pool_size = 4G
innodb_log_file_size = 512M
innodb_flush_method = O_DIRECT
innodb_flush_log_at_trx_commit = 2
```

2. **Apache Optimization**
```bash
# Edit Apache configuration
sudo nano /etc/apache2/apache2.conf

# Recommended settings
KeepAlive On
KeepAliveTimeout 5
MaxKeepAliveRequests 100
```

### Security Hardening

1. **File Permissions**
```bash
# Secure sensitive files
sudo chmod 600 /var/www/eyenet/config.php
sudo chmod 600 /var/www/eyenet/.env
```

2. **Regular Updates**
```bash
# Create update script
sudo nano /usr/local/bin/update-system.sh

# Script content
#!/bin/bash
apt update
apt upgrade -y
apt autoremove -y
```

For additional support or troubleshooting, please refer to:
- [Official Documentation](https://your-docs-url)
- [GitHub Issues](https://github.com/your-repo/eyenet/issues)
- [Community Forum](https://your-forum-url)
