#!/bin/bash

# EyeNet Auto-Installer for Linux
# This script will automatically install and configure EyeNet monitoring system

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

echo -e "${GREEN}Starting EyeNet Linux Auto-Installer...${NC}"

# Function to check if a command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Install required packages
install_dependencies() {
    echo -e "${YELLOW}Installing dependencies...${NC}"
    if command_exists apt-get; then
        apt-get update
        apt-get install -y apache2 php mysql-server php-mysql php-curl php-gd php-mbstring php-xml php-zip unzip git
    elif command_exists yum; then
        yum update -y
        yum install -y httpd php mysql mysql-server php-mysql php-curl php-gd php-mbstring php-xml php-zip unzip git
    else
        echo -e "${RED}Unsupported package manager. Please install dependencies manually.${NC}"
        exit 1
    fi
}

# Configure Apache
configure_apache() {
    echo -e "${YELLOW}Configuring Apache...${NC}"
    if command_exists apt-get; then
        systemctl enable apache2
        systemctl start apache2
    elif command_exists yum; then
        systemctl enable httpd
        systemctl start httpd
    fi
}

# Configure MySQL
configure_mysql() {
    echo -e "${YELLOW}Configuring MySQL...${NC}"
    systemctl enable mysql
    systemctl start mysql
    
    # Generate random password for root
    MYSQL_ROOT_PASSWORD=$(openssl rand -base64 12)
    
    # Secure MySQL installation
    mysql -e "UPDATE mysql.user SET Password=PASSWORD('${MYSQL_ROOT_PASSWORD}') WHERE User='root';"
    mysql -e "DELETE FROM mysql.user WHERE User='';"
    mysql -e "DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');"
    mysql -e "DROP DATABASE IF EXISTS test;"
    mysql -e "DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%';"
    mysql -e "FLUSH PRIVILEGES;"
    
    echo -e "${GREEN}MySQL root password: ${MYSQL_ROOT_PASSWORD}${NC}"
    echo "MySQL root password: ${MYSQL_ROOT_PASSWORD}" > /root/eyenet_mysql_credentials.txt
}

# Install EyeNet
install_eyenet() {
    echo -e "${YELLOW}Installing EyeNet...${NC}"
    
    # Create database
    DB_NAME="eyenet"
    DB_USER="eyenet_user"
    DB_PASS=$(openssl rand -base64 12)
    
    mysql -u root -p"${MYSQL_ROOT_PASSWORD}" -e "CREATE DATABASE ${DB_NAME};"
    mysql -u root -p"${MYSQL_ROOT_PASSWORD}" -e "CREATE USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
    mysql -u root -p"${MYSQL_ROOT_PASSWORD}" -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';"
    mysql -u root -p"${MYSQL_ROOT_PASSWORD}" -e "FLUSH PRIVILEGES;"
    
    # Clone EyeNet repository
    cd /var/www/html
    git clone https://github.com/yourusername/EyeNet_PHP-New.git eyenet
    
    # Set permissions
    chown -R www-data:www-data /var/www/html/eyenet
    chmod -R 755 /var/www/html/eyenet
    
    # Create configuration file
    cat > /var/www/html/eyenet/config/database.php << EOF
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', '${DB_NAME}');
define('DB_USER', '${DB_USER}');
define('DB_PASS', '${DB_PASS}');
?>
EOF
    
    # Save credentials
    echo "EyeNet Database Credentials:" >> /root/eyenet_mysql_credentials.txt
    echo "Database: ${DB_NAME}" >> /root/eyenet_mysql_credentials.txt
    echo "Username: ${DB_USER}" >> /root/eyenet_mysql_credentials.txt
    echo "Password: ${DB_PASS}" >> /root/eyenet_mysql_credentials.txt
}

# Configure firewall
configure_firewall() {
    echo -e "${YELLOW}Configuring firewall...${NC}"
    if command_exists ufw; then
        ufw allow 80/tcp
        ufw allow 443/tcp
    elif command_exists firewall-cmd; then
        firewall-cmd --permanent --add-service=http
        firewall-cmd --permanent --add-service=https
        firewall-cmd --reload
    fi
}

# Main installation process
main() {
    install_dependencies
    configure_apache
    configure_mysql
    install_eyenet
    configure_firewall
    
    echo -e "${GREEN}Installation completed!${NC}"
    echo -e "${GREEN}Please check /root/eyenet_mysql_credentials.txt for database credentials${NC}"
    echo -e "${GREEN}Access EyeNet at: http://your-server-ip/eyenet${NC}"
}

# Start installation
main
