#!/bin/bash
set -e

# Install Composer dependencies if vendor directory doesn't exist
if [ ! -d "/var/www/html/vendor" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction --optimize-autoloader --no-dev
fi

# Create storage directory if it doesn't exist
if [ ! -d "/var/www/html/storage" ]; then
    echo "Creating storage directory..."
    mkdir -p /var/www/html/storage
    chmod 777 /var/www/html/storage
fi

# Execute the main container command (start Apache)
exec apache2-foreground
