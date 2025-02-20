#!/bin/bash

# EyeNet Auto-Installer for Bitnami LAMP Stack
# This script will automatically install and configure EyeNet monitoring system on Bitnami LAMP

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo -e "${RED}Please run as root${NC}"
    exit 1
fi

# Check if this is a Bitnami LAMP installation
if [ ! -d "/opt/bitnami" ]; then
    echo -e "${RED}Bitnami LAMP stack not found. Please install Bitnami LAMP first.${NC}"
    exit 1
fi

echo -e "${GREEN}Starting EyeNet Bitnami LAMP Auto-Installer...${NC}"

# Function to check if a command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Install additional PHP extensions
install_php_extensions() {
    echo -e "${YELLOW}Installing PHP extensions...${NC}"
    /opt/bitnami/php/bin/pecl install curl
    /opt/bitnami/php/bin/pecl install mbstring
    /opt/bitnami/php/bin/pecl install xml
    
    # Restart PHP-FPM
    /opt/bitnami/ctlscript.sh restart php-fpm
}

# Install EyeNet
install_eyenet() {
    echo -e "${YELLOW}Installing EyeNet...${NC}"
    
    # Database credentials
    DB_NAME="eyenet"
    DB_USER="eyenet_user"
    DB_PASS=$(openssl rand -base64 12)
    
    # Create database and user
    /opt/bitnami/mysql/bin/mysql -u root -e "CREATE DATABASE ${DB_NAME};"
    /opt/bitnami/mysql/bin/mysql -u root -e "CREATE USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
    /opt/bitnami/mysql/bin/mysql -u root -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';"
    /opt/bitnami/mysql/bin/mysql -u root -e "FLUSH PRIVILEGES;"
    
    # Clone EyeNet repository
    cd /opt/bitnami/apache2/htdocs
    git clone https://github.com/yourusername/EyeNet_PHP-New.git eyenet
    
    # Set permissions
    chown -R daemon:daemon /opt/bitnami/apache2/htdocs/eyenet
    chmod -R 755 /opt/bitnami/apache2/htdocs/eyenet
    
    # Create configuration file
    cat > /opt/bitnami/apache2/htdocs/eyenet/config/database.php << EOF
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', '${DB_NAME}');
define('DB_USER', '${DB_USER}');
define('DB_PASS', '${DB_PASS}');
?>
EOF
    
    # Save credentials
    echo "EyeNet Database Credentials:" > /root/eyenet_bitnami_credentials.txt
    echo "Database: ${DB_NAME}" >> /root/eyenet_bitnami_credentials.txt
    echo "Username: ${DB_USER}" >> /root/eyenet_bitnami_credentials.txt
    echo "Password: ${DB_PASS}" >> /root/eyenet_bitnami_credentials.txt
}

# Configure Apache Virtual Host
configure_apache() {
    echo -e "${YELLOW}Configuring Apache virtual host...${NC}"
    
    # Create virtual host configuration
    cat > /opt/bitnami/apache2/conf/vhosts/eyenet-vhost.conf << EOF
<VirtualHost *:80>
    ServerName eyenet.local
    DocumentRoot "/opt/bitnami/apache2/htdocs/eyenet"
    
    <Directory "/opt/bitnami/apache2/htdocs/eyenet">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "logs/eyenet-error.log"
    CustomLog "logs/eyenet-access.log" combined
</VirtualHost>
EOF
    
    # Restart Apache
    /opt/bitnami/ctlscript.sh restart apache
}

# Main installation process
main() {
    install_php_extensions
    install_eyenet
    configure_apache
    
    echo -e "${GREEN}Installation completed!${NC}"
    echo -e "${GREEN}Please check /root/eyenet_bitnami_credentials.txt for database credentials${NC}"
    echo -e "${GREEN}Access EyeNet at: http://your-server-ip/eyenet${NC}"
    echo -e "${YELLOW}Note: You may need to configure your DNS or hosts file to use eyenet.local${NC}"
}

# Start installation
main
