# Magento-2
The payment extension for Magento 2, support WeChat Pay and Alipay

Magento® 2 use the Composer to manage the module package and the library. Composer is a dependency manager for PHP. Composer declare the libraries your project depends on and it will manage (install/update) them for you.

# Check Composer Status
Check if your server has composer installed by running the following command:
```shell
composer –v
```
If your server doesn’t have the composer install, you can easily install it. 
[https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)

# Install using Composer
Step-by-step to install the Magento® 2 extension by Composer:

Run the ssh console.
Locate your Root
Install the Magento® 2 extension
Cache and Deploy
Run your SSH Console to connect to your Magento® 2 store

Locate the root of your Magento® 2 store.

Enter the command line in your Root and wait as composer will download the extension for you:
```shell
composer require yabandpay/payment
```
When it’s finished you can activate the extension, clean the caches and deploy the content in your Magento® environment using the following command line;
```shell
php bin/magento module:enable YaBandPay_Payment
php bin/magento setup:upgrade
php bin/magento cache:clean
```
If Magento® is running in production mode, deploy the static content:
```shell
php bin/magento setup:static-content:deploy
```
After the installation. Go to your Magento® admin portal, to 'Stores' > 'Configuration’ > 'Sales’ > 'Payment Methods’ > 'YaBandPay Payment’. Select 'General’.
