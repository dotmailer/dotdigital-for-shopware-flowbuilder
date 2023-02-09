# Dotdigital for Shopware 6 - Flow Builder
[![Packagist Version](https://img.shields.io/packagist/v/dotdigital/dotdigital-for-shopware-flowbuilder?color=green&label=stable)](https://github.com/dotmailer/dotdigital-for-shopware-flowbuilder/releases)
[![Packagist Version (including pre-releases)](https://img.shields.io/packagist/v/dotdigital/dotdigital-for-shopware-flowbuilder?color=blue&include_prereleases&label=feature)](https://github.com/dotmailer/dotdigital-for-shopware-flowbuilder/releases)
[![license](https://img.shields.io/github/license/mashape/apistatus.svg)](LICENSE.md)

Shopware merchants can now integrate with Dotdigital using Flow Builder actions.

## Installation
To install this plugin you can either:
1. Clone the code into your Shopware installation in custom/plugins
2. Download the code from Github and upload it via Extensions > My extensions > Upload extension
3. Install using composer:
```
composer require dotdigital/dotdigital-for-shopware-flowbuilder
```

## Activation
Again, you have two options:
1. bin/console plugin:install --activate DotdigitalFlow 
2. Activate the plugin via Extensions > My extensions

Finally, it is necessary to run:
```
bash bin/build-administration.sh
```
in order to rebuild the JS for Flow Builder.

## Changelog

### 1.1.0

#### What's new
- We've added a new flow action to add or update contacts in Dotdigital. Email addresses can be added to address books, with data fields, and options for resubscribe and double opt-in.
- We've added a new flow action to enroll contacts to marketing programs in Dotdigital.

#### Improvements
- Merchants using our transactional email flow action can now choose from a list of triggered campaigns to send from Dotdigital.

### 1.0.0

#### What's new
- Merchants can define flows with a new Dotdigital action to send triggered non-marketing email campaigns in Dotdigital to nominated recipients (regular email addresses and variables are supported).
