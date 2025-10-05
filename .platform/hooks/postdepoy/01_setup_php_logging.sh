#!/bin/bash

# Create the log file and set permissions
touch /var/log/php-fpm/www-error.log
chmod 666 /var/log/php-fpm/www-error.log
chown webapp:webapp /var/log/php-fpm/www-error.log

# Configure PHP-FPM to use the new error log
echo "php_admin_value[error_log] = /var/log/php-fpm/www-error.log" >> /etc/php-fpm.d/www.conf
echo "php_admin_flag[log_errors] = on" >> /etc/php-fpm.d/www.conf

# Restart PHP-FPM to apply changes
systemctl restart php-fpm.service