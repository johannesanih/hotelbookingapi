php -m | grep -i pdo   # lists loaded PDO drivers
php -i | grep -i sqlite   # check if pdo_sqlite appears

# Update package list
sudo apt update

# Install the PDO SQLite extension for your PHP version (adjust if not 8.3)
# This works for PHP 8.2/8.3/8.4 in most Codespaces setups
sudo apt install -y php8.3-sqlite3   # or php8.2-sqlite3 / php8.4-sqlite3 if your php -v shows different

# If above fails with "package not found", use the Ondřej PPA (reliable for PHP extensions)
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.3-sqlite3

# Restart isn't usually needed in Codespaces, but reload PHP config
php -m | grep sqlite   # should now show pdo_sqlite and sqlite3
