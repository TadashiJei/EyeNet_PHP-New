#!/bin/bash

# EyeNet Linux Server Setup Script
# This script automates the setup process for EyeNet on Linux servers

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored messages
print_message() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Function to check if command was successful
check_status() {
    if [ $? -eq 0 ]; then
        print_message "$1 successful"
    else
        print_error "$1 failed"
        exit 1
    fi
}

# Check if script is run as root
if [[ $EUID -ne 0 ]]; then
   print_error "This script must be run as root"
   exit 1
fi

# Detect Linux distribution
if [ -f /etc/os-release ]; then
    . /etc/os-release
    OS=$NAME
    VERSION=$VERSION_ID
else
    print_error "Cannot detect Linux distribution"
    exit 1
fi

print_message "Detected OS: $OS $VERSION"

# Function to install packages for Ubuntu/Debian
install_debian_packages() {
    print_message "Updating system packages..."
    apt update && apt upgrade -y
    check_status "System update"

    print_message "Installing required packages..."
    apt install -y apache2 \
        php8.1 php8.1-cli php8.1-common php8.1-mysql \
        php8.1-zip php8.1-gd php8.1-mbstring php8.1-curl \
        php8.1-xml php8.1-bcmath php8.1-ssh2 \
        mariadb-server git curl wget zip unzip \
        net-tools htop ntp
    check_status "Package installation"
}

# Function to install packages for CentOS/RHEL
install_centos_packages() {
    print_message "Updating system packages..."
    dnf update -y
    check_status "System update"

    print_message "Installing EPEL repository..."
    dnf install -y epel-release
    check_status "EPEL installation"

    print_message "Installing required packages..."
    dnf install -y httpd php php-cli php-common php-mysql \
        php-zip php-gd php-mbstring php-curl php-xml \
        php-bcmath php-ssh2 mariadb-server git curl wget \
        zip unzip net-tools htop chrony
    check_status "Package installation"
}

# Install packages based on OS
case "$OS" in
    *"Ubuntu"*|*"Debian"*)
        install_debian_packages
        ;;
    *"CentOS"*|*"Red Hat"*)
        install_centos_packages
        ;;
    *)
        print_error "Unsupported Linux distribution: $OS"
        exit 1
        ;;
esac

# Configure timezone
print_message "Configuring timezone..."
timedatectl set-timezone Asia/Singapore
check_status "Timezone configuration"

# Start and enable services
print_message "Starting services..."
if [[ "$OS" == *"Ubuntu"* || "$OS" == *"Debian"* ]]; then
    systemctl enable apache2
    systemctl start apache2
    systemctl enable mysql
    systemctl start mysql
else
    systemctl enable httpd
    systemctl start httpd
    systemctl enable mariadb
    systemctl start mariadb
fi
check_status "Service initialization"

# Secure MySQL installation
print_message "Securing MySQL installation..."
mysql_secure_installation
check_status "MySQL security configuration"

# Create EyeNet database and user
print_message "Creating database and user..."
read -p "Enter MySQL root password: " MYSQL_ROOT_PASS
read -p "Enter desired EyeNet database password: " EYENET_DB_PASS

mysql -uroot -p"$MYSQL_ROOT_PASS" <<EOF
CREATE DATABASE IF NOT EXISTS eyenet;
CREATE USER IF NOT EXISTS 'eyenet'@'localhost' IDENTIFIED BY '$EYENET_DB_PASS';
GRANT ALL PRIVILEGES ON eyenet.* TO 'eyenet'@'localhost';
FLUSH PRIVILEGES;
EOF
check_status "Database creation"

# Configure PHP
print_message "Configuring PHP..."
PHP_INI="/etc/php/8.1/apache2/php.ini"
if [[ "$OS" == *"CentOS"* || "$OS" == *"Red Hat"* ]]; then
    PHP_INI="/etc/php.ini"
fi

sed -i 's/memory_limit = .*/memory_limit = 256M/' $PHP_INI
sed -i 's/upload_max_filesize = .*/upload_max_filesize = 64M/' $PHP_INI
sed -i 's/post_max_size = .*/post_max_size = 64M/' $PHP_INI
sed -i 's/max_execution_time = .*/max_execution_time = 300/' $PHP_INI
sed -i 's/date.timezone = .*/date.timezone = Asia\/Singapore/' $PHP_INI
check_status "PHP configuration"

# Install Composer
print_message "Installing Composer..."
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
check_status "Composer installation"

# Set up web directory
print_message "Setting up web directory..."
mkdir -p /var/www/eyenet
chown -R www-data:www-data /var/www/eyenet
chmod -R 755 /var/www/eyenet
check_status "Web directory setup"

# Configure Apache virtual host
print_message "Configuring Apache virtual host..."
if [[ "$OS" == *"Ubuntu"* || "$OS" == *"Debian"* ]]; then
    cat > /etc/apache2/sites-available/eyenet.conf <<EOF
<VirtualHost *:80>
    ServerName eyenet.local
    DocumentRoot /var/www/eyenet
    
    <Directory /var/www/eyenet>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog \${APACHE_LOG_DIR}/eyenet_error.log
    CustomLog \${APACHE_LOG_DIR}/eyenet_access.log combined
</VirtualHost>
EOF
    a2ensite eyenet.conf
    a2enmod rewrite
    systemctl restart apache2
else
    cat > /etc/httpd/conf.d/eyenet.conf <<EOF
<VirtualHost *:80>
    ServerName eyenet.local
    DocumentRoot /var/www/eyenet
    
    <Directory /var/www/eyenet>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog /var/log/httpd/eyenet_error.log
    CustomLog /var/log/httpd/eyenet_access.log combined
</VirtualHost>
EOF
    systemctl restart httpd
fi
check_status "Apache configuration"

# Set up log rotation
print_message "Configuring log rotation..."
cat > /etc/logrotate.d/eyenet <<EOF
/var/www/eyenet/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
}
EOF
check_status "Log rotation setup"

# Set up cron jobs
print_message "Setting up cron jobs..."
(crontab -l 2>/dev/null; echo "*/5 * * * * php /var/www/eyenet/crons/predictive_analysis.php") | crontab -
(crontab -l 2>/dev/null; echo "*/1 * * * * php /var/www/eyenet/crons/server_check.php") | crontab -
(crontab -l 2>/dev/null; echo "0 0 * * * php /var/www/eyenet/crons/cleanup.php") | crontab -
check_status "Cron job setup"

print_message "Setup complete! Please complete these manual steps:"
echo "1. Update your hosts file to include eyenet.local"
echo "2. Copy config.example.php to config.php and update the settings"
echo "3. Import the database schema from install/sql/"
echo "4. Configure your firewall rules"
echo "5. Set up SSL certificate (recommended)"

print_message "EyeNet installation completed successfully!"
