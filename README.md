[![Latest Stable Version](https://poser.pugx.org/lizardmedia/module-password-migrator/v/stable)](https://packagist.org/packages/lizardmedia/module-password-migrator)
[![Total Downloads](https://poser.pugx.org/lizardmedia/module-password-migrator/downloads)](https://packagist.org/packages/lizardmedia/module-password-migrator)
[![License](https://poser.pugx.org/lizardmedia/module-password-migrator/license)](https://packagist.org/packages/lizardmedia/module-password-migrator)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/lizardmedia/password-migrator-magento2/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/lizardmedia/password-migrator-magento2/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/lizardmedia/password-migrator-magento2/badges/build.png?b=master)](https://scrutinizer-ci.com/g/lizardmedia/password-migrator-magento2/build-status/master)


# Lizard Media PasswordMigrator Magento2 module

A Magento2 module which helps you migrate user passwords when migrating data from
an existing e-commerce system. 

You have to save old customer passwords (possibly with salt). When a customer fails to
log in the module checks if the password provided is correct for the legacy system and
saves the password after encypting it using Magento build-in service and logs the customer in.

#### Features ####

* Allows customers to log into Magento using passwords from legacy e-commerce system
* Saves customers password as build-in Magento password
* Clears the legacy password table after a year/2 years/never since data migration

## Prerequisites ##

* Magento 2.2 or higher
* PHP 7.1

## Installing ##

You can install the module by downloading a .zip file and unpacking it inside
``app/code/LizardMedia/PasswordMigrator`` directory inside your Magento
or via Composer (required).

To install the module via Composer simply run
```
composer require lizardmedia/module-password-migrator
```

Than enable the module by running these command in the root of your Magento installation
```
bin/magento module:enable LizardMedia_PasswordMigrator
bin/magento setup:upgrade
```

## Usage ##

#### Implementation of legacy hashing method ####
In order to use the module you are required to create a module dependant on this module and 
implement the interface
```
\LizardMedia\PasswordMigrator\Api\LegacyEncryptorInterface
```
containing the hashing method for your legacy system.

You have to create a preference using ``etc/di.xml`` in your custom module for the interface.

#### Adding legacy passwords ####

To insert legacy passwords you should use
```
LizardMedia\PasswordMigrator\Api\Data\PasswordRepositoryInterface
```
with
```
LizardMedia\PasswordMigrator\Api\Data\PasswordInterface
```
objects.

The aboce are the only to actions required to use the module.

#### Automatic cleanup configuration ####

Each legacy password is removed after being used by the customer. You can configure the
module to clean the legacy passwords table after:
* a year
* half a year (default)
* never

The configuration is available in the backend of your site at
``Stores->Configuration->Password Migrator``

## Versioning ##

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/your/project/tags).

## Authors

* **Maciej Sławik** - [Lizard Media](https://github.com/lizardmedia)

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details 