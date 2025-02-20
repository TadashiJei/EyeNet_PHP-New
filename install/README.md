# EyeNet Auto-Installers

This directory contains auto-installer scripts for setting up EyeNet on different environments.

## Linux Auto-Installer

The `linux-auto-install.sh` script will automatically install EyeNet on a fresh Linux installation. It supports both Debian/Ubuntu (apt) and RHEL/CentOS (yum) based systems.

### Prerequisites
- Root access
- Git installed
- Internet connection

### Installation
1. Make the script executable:
```bash
chmod +x linux-auto-install.sh
```

2. Run the installer:
```bash
sudo ./linux-auto-install.sh
```

3. After installation, check `/root/eyenet_mysql_credentials.txt` for database credentials.

## Bitnami LAMP Auto-Installer

The `bitnami-lamp-install.sh` script will install EyeNet on an existing Bitnami LAMP stack installation.

### Prerequisites
- Bitnami LAMP stack installed
- Root access
- Git installed
- Internet connection

### Installation
1. Make the script executable:
```bash
chmod +x bitnami-lamp-install.sh
```

2. Run the installer:
```bash
sudo ./bitnami-lamp-install.sh
```

3. After installation, check `/root/eyenet_bitnami_credentials.txt` for database credentials.

## Post-Installation

After running either installer:

1. Access EyeNet at: `http://your-server-ip/eyenet`
2. Log in with the default credentials:
   - Username: admin
   - Password: admin123

3. Change the default password immediately after first login.

## Security Notes

- The installers generate random passwords for database users
- All credentials are saved in protected files only accessible by root
- It's recommended to change the default admin password immediately
- Consider setting up SSL/TLS for secure access

## Troubleshooting

If you encounter any issues:

1. Check the Apache error logs:
   - Linux: `/var/log/apache2/error.log` or `/var/log/httpd/error.log`
   - Bitnami: `/opt/bitnami/apache2/logs/error.log`

2. Check the MySQL error logs:
   - Linux: `/var/log/mysql/error.log`
   - Bitnami: `/opt/bitnami/mysql/logs/mysqld.log`

3. Ensure all required PHP extensions are installed
4. Verify database connection settings in `config/database.php`

## Support

For additional support or to report issues, please visit our GitHub repository or contact support@eyenet.com
