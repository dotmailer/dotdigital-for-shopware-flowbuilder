# Dotdigital for Shopware 6 - Flow Builder
[![Packagist Version](https://img.shields.io/packagist/v/dotdigital/dotdigital-for-shopware-flowbuilder?color=green&label=stable)](https://github.com/dotmailer/dotdigital-for-shopware-flowbuilder/releases)
[![Packagist Version (including pre-releases)](https://img.shields.io/packagist/v/dotdigital/dotdigital-for-shopware-flowbuilder?color=blue&include_prereleases&label=feature)](https://github.com/dotmailer/dotdigital-for-shopware-flowbuilder/releases)
[![license](https://img.shields.io/github/license/mashape/apistatus.svg)](LICENSE.md)

Shopware's merchants can now integrate with Dotdigital using Flow Builder actions. Merchants can define flows with a new Dotdigital action to send triggered non-marketing email campaigns in Dotdigital to nominated recipients (regular email addresses and variables are supported).

[Read the documentation](https://support.dotdigital.com/hc/en-gb/sections/7614571932178-Shopware-Flows)

## Requirements
- PHP 8.1+
- Shopware 6.5+
  - Shopware 6.4.x is compatible with the plugin's 1.x release line.

## Installation

### Step 1 - get the files
To install this plugin you have two options:

#### Platform installation
- Download the code from Github and upload it via Extensions > My extensions > Upload extension
- Activate the plugin via Extensions > My extensions

OR: 

#### Manual installation via git or composer
Git install
```
git clone git@github.com:dotmailer/dotdigital-for-shopware-flowbuilder.git custom/plugins/DotdigitalFlow
```
Composer install
```
composer require dotdigital/dotdigital-for-shopware-flowbuilder
```

### Step 2 - Install the plugin
Refresh the plugin list:
```
bin/console plugin:refresh
```
Install the plugin:
```
bin/console plugin:install --activate DotdigitalFlow
```

### Step 3 - Build extension assets
```
bash bin/build-administration.sh
```

## Changelog

### 2.0.1

#### Improvements
- We removed an unused JS file.
- The plugin's minimum-stability is reset to 'stable' following the release of Shopware 6.5.

### 2.0.0

#### What's new
- The plugin is now compatible with Shopware 6.5+
[NOTE: Shopware 6.4.x will not be able to run this version of the plugin.]

### 1.1.1

#### Bug fixes
- We fixed an error in validation-directive.js relating to unsupported 'classProperties'.

### 1.1.0

#### What's new
- We've added a new flow action to add or update contacts in Dotdigital. Email addresses can be added to address books, with data fields, and options for resubscribe and double opt-in.
- We've added a new flow action to enroll contacts to marketing programs in Dotdigital.

#### Improvements
- Merchants using our transactional email flow action can now choose from a list of triggered campaigns to send from Dotdigital.

### 1.0.0

#### What's new
- Merchants can define flows with a new Dotdigital action to send triggered non-marketing email campaigns in Dotdigital to nominated recipients (regular email addresses and variables are supported).
