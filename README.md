# 1. Script Task

# Database (schema) parameter is missing, please create a database called dbguillermo
CREATE DATABASE IF NOT EXISTS catalyst DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# For Creating the table users run
php user_upload.php --create_table -u root -h 127.0.0.1

# For insert data from csv into table users run
php user_upload.php --file users.csv -u root -h 127.0.0.1

# For dry run
php user_upload.php --file users.csv --dry_run



# 2. Logic Test

# To Run this script please type on shell
php foobar.php