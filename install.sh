#!/bin/bash
#
#////////////////////////////////////////////////////////////
#===========================================================
# EyeNet - Installer
#===========================================================
# Set environment
PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin

# Clear the screen
clear

#SERVERKEY=$1
#GATEWAY=$2
LOG=/tmp/eyenet.log

echo "--------------------------------"
echo " Welcome to EyeNet Agent Installer"
echo "--------------------------------"
echo " "

# Are we running as root
if [ $(id -u) != "0" ]; then
	echo "eyenet Agent installer needs to be run with root priviliges"
	echo "Try again with root privilileges"
	exit 1;
fi

# Is the server key parameter given ?
if [ $# -lt 2 ]; then
	echo "The server key or gateway is missing"
	echo "Exiting installer"
	exit 1;
fi

### install Dependencies here
echo "Installing Dependencies"

# RHEL / CentOS / etc
if [ -n "$(command -v yum)" ]; then
	yum -y install cronie gzip curl >> $LOG 2>&1
	service crond start >> $LOG 2>&1
	chkconfig crond on >> $LOG 2>&1

	# Check if perl available or not
	if ! type "perl" >> $LOG 2>&1; then
		yum -y install perl >> $LOG 2>&1
	fi

	# Check if unzip available or not
	if ! type "unzip" >> $LOG 2>&1; then
		yum -y install unzip >> $LOG 2>&1
	fi

	# Check if curl available or not
	if ! type "curl" >> $LOG 2>&1; then
		yum -y install curl >> $LOG 2>&1
	fi
fi

# Debian / Ubuntu
if [ -n "$(command -v apt-get)" ]; then
	apt-get update -y >> $LOG 2>&1
	apt-get install -y cron curl gzip >> $LOG 2>&1
	service cron start >> $LOG 2>&1

	# Check if perl available or not
	if ! type "perl" >> $LOG 2>&1; then
		apt-get install -y perl >> $LOG 2>&1
	fi

	# Check if unzip available or not
	if ! type "unzip" >> $LOG 2>&1; then
		apt-get install -y unzip >> $LOG 2>&1
	fi

	# Check if curl available or not
	if ! type "curl" >> $LOG 2>&1; then
		apt-get install -y curl >> $LOG 2>&1
	fi
fi

# Ubuntu-specific web server setup
if [ -n "$(command -v apt-get)" ]; then
    echo "Setting up web server and PHP..."
    apt-get install -y apache2 php php-mysql php-curl php-xml php-mbstring >> $LOG 2>&1
    
    # Enable required Apache modules
    a2enmod rewrite
    a2enmod ssl
    
    # Restart Apache
    systemctl restart apache2
    systemctl enable apache2
    
    # Install MySQL if not present
    if ! type "mysql" >> $LOG 2>&1; then
        echo "Installing MySQL Server..."
        apt-get install -y mysql-server >> $LOG 2>&1
        systemctl start mysql
        systemctl enable mysql
    fi
    
    # Set up virtual host
    echo "Setting up Virtual Host..."
    read -p "Enter your domain name (e.g., eyenet.example.com): " DOMAIN_NAME
    
    # Create Apache virtual host configuration
    cat > /etc/apache2/sites-available/eyenet.conf << EOF
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    ServerName $DOMAIN_NAME
    DocumentRoot /var/www/html/eyenet
    
    <Directory /var/www/html/eyenet>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog \${APACHE_LOG_DIR}/eyenet-error.log
    CustomLog \${APACHE_LOG_DIR}/eyenet-access.log combined
</VirtualHost>
EOF
    
    # Enable the site
    a2ensite eyenet.conf
    
    # Create web directory if it doesn't exist
    mkdir -p /var/www/html/eyenet
    
    # Set proper permissions
    chown -R www-data:www-data /var/www/html/eyenet
    chmod -R 755 /var/www/html/eyenet
    
    # Restart Apache to apply changes
    systemctl restart apache2
    
    echo "Web server setup completed!"
    echo "Please ensure to:"
    echo "1. Point your domain DNS A record to this server's IP address"
    echo "2. Configure your database in config.php"
    echo "3. Consider setting up SSL using Let's Encrypt"
fi

# ArchLinux
if [ -n "$(command -v pacman)" ]; then
	pacman -Sy  >> $LOG 2>&1
	pacman -S --noconfirm cronie curl gzip >> $LOG 2>&1
	systemctl start cronie >> $LOG 2>&1
	systemctl enable cronie >> $LOG 2>&1

	# Check if perl available or not
	if ! type "perl" >> $LOG 2>&1; then
		pacman -S --noconfirm perl >> $LOG 2>&1
	fi

	# Check if unzip available or not
	if ! type "unzip" >> $LOG 2>&1; then
		pacman -S --noconfirm unzip >> $LOG 2>&1
	fi

	# Check if curl available or not
	if ! type "curl" >> $LOG 2>&1; then
		pacman -S --noconfirm curl >> $LOG 2>&1
	fi
fi


# OpenSuse
if [ -n "$(command -v zypper)" ]; then
	zypper --non-interactive install cronie curl gzip >> $LOG 2>&1
	service cron start >> $LOG 2>&1

	# Check if perl available or not
	if ! type "perl" >> $LOG 2>&1; then
		zypper --non-interactive install perl >> $LOG 2>&1
	fi

	# Check if unzip available or not
	if ! type "unzip" >> $LOG 2>&1; then
		zypper --non-interactive install unzip >> $LOG 2>&1
	fi

	# Check if curl available or not
	if ! type "curl" >> $LOG 2>&1; then
		zypper --non-interactive install curl >> $LOG 2>&1
	fi
fi


# Gentoo
if [ -n "$(command -v emerge)" ]; then

	# Check if crontab is present or not available or not
	if ! type "crontab" >> $LOG 2>&1; then
		emerge cronie >> $LOG 2>&1
		/etc/init.d/cronie start >> $LOG 2>&1
		rc-update add cronie default >> $LOG 2>&1
 	fi

	# Check if perl available or not
	if ! type "perl" >> $LOG 2>&1; then
		emerge perl >> $LOG 2>&1
	fi

	# Check if unzip available or not
	if ! type "unzip" >> $LOG 2>&1; then
		emerge unzip >> $LOG 2>&1
	fi

	# Check if curl available or not
	if ! type "curl" >> $LOG 2>&1; then
		emerge net-misc/curl >> $LOG 2>&1
	fi

	# Check if gzip available or not
	if ! type "gzip" >> $LOG 2>&1; then
		emerge gzip >> $LOG 2>&1
	fi
fi


# Slackware
if [ -f "/etc/slackware-version" ]; then

	if [ -n "$(command -v slackpkg)" ]; then

		# Check if crontab is present or not available or not
		if ! type "crontab" >> $LOG 2>&1; then
			slackpkg -dialog=off -batch=on -default_answer=y install dcron >> $LOG 2>&1
		fi

		# Check if perl available or not
		if ! type "perl" >> $LOG 2>&1; then
			slackpkg -dialog=off -batch=on -default_answer=y install perl >> $LOG 2>&1
		fi

		# Check if unzip available or not
		if ! type "unzip" >> $LOG 2>&1; then
			slackpkg -dialog=off -batch=on -default_answer=y install infozip >> $LOG 2>&1
		fi

		# Check if curl available or not
		if ! type "curl" >> $LOG 2>&1; then
			slackpkg -dialog=off -batch=on -default_answer=y install curl >> $LOG 2>&1
		fi

		# Check if gzip available or not
		if ! type "gzip" >> $LOG 2>&1; then
			slackpkg -dialog=off -batch=on -default_answer=y install gzip >> $LOG 2>&1
		fi

	else
		echo "Please install slackpkg and re-run installation."
		exit 1;
	fi
fi


# Is Cron available?
if [ ! -n "$(command -v crontab)" ]; then
	echo "Cron is required but we could not install it."
	echo "Exiting installer"
	exit 1;
fi

# Is CURL available?
if [  ! -n "$(command -v curl)" ]; then
	echo "CURL is required but we could not install it."
	echo "Exiting installer"
	exit 1;
fi

# Remove previous installation
if [ -f /opt/eyenet/agent.sh ]; then
	# Remove folder
	rm -rf /opt/eyenet
	# Remove crontab
	crontab -r -u eyenetagent >> $LOG 2>&1
	# Remove user
	userdel eyenetagent >> $LOG 2>&1
fi

### Install ###
mkdir -p /opt/eyenet >> $LOG 2>&1
wget -N --no-check-certificate -O /opt/eyenet/agent.sh $2/assets/agent.sh >> $LOG 2>&1
wget -N --no-check-certificate -O /opt/eyenet/uninstall.sh $2/assets/uninstall.sh >> $LOG 2>&1

echo "$1" > /opt/eyenet/serverkey
echo "$2/agent.php" > /opt/eyenet/gateway

# Did it download ?
if ! [ -f /opt/eyenet/agent.sh ]; then
	echo "Unable to install!"
	echo "Exiting installer"
	exit 1;
fi

#useradd eyenetagent -r -d /opt/eyenet -s /bin/false >> $LOG 2>&1
#groupadd eyenetagent >> $LOG 2>&1
#usermod -a -G sudo eyenetagent

crontab -r -u eyenetagent >> $LOG 2>&1
userdel eyenetagent >> $LOG 2>&1

# Disable cagefs for eyenet
if [ -f /usr/sbin/cagefsctl ]; then
	/usr/sbin/cagefsctl --disable eyenetagent >> $LOG 2>&1
fi

# Modify user permissions
#chown -R eyenetagent:eyenetagent /opt/eyenet && chmod -R 700 /opt/eyenet >> $LOG 2>&1

# Configure cron
if ! crontab -u root -l | grep '* * * * * bash /opt/eyenet/agent.sh > /opt/eyenet/cron.log 2>&1' &> /dev/null
then
	crontab -u root -l 2>/dev/null | { cat; echo "* * * * * bash /opt/eyenet/agent.sh > /opt/eyenet/cron.log 2>&1"; } | crontab -u root -
fi



echo " "
echo "-------------------------------------"
echo " Installation Completed "
echo "-------------------------------------"


# Attempt to delete this installer
if [ -f $0 ]; then
	rm -f $0
fi
