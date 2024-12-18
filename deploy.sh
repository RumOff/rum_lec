#!/bin/bash

echo '### Pulling latest code from Git'
git pull origin main

echo '### Clearing Laravel caches'
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo '### Installing Composer dependencies'
composer install --no-dev --optimize-autoloader

echo '### Running database migrations'
php artisan migrate --force

echo '### Restarting Supervisor workers'
sudo /home/ec2-user/.local/bin/supervisorctl -c /home/ec2-user/etc/supervisord.conf restart all

echo '### Deployment completed!'
