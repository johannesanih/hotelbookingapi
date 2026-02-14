sudo apt update
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y   # reliable PHP repo
sudo apt update
sudo apt install -y php8.3 php8.3-cli php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-gd php8.3-intl php8.3-bcmath unzip

# Composer
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

# Verify
php -v
composer --version
