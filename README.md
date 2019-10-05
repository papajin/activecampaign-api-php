# activecampaign-api-php
[![Source Code][badge-source]][source]
[![Latest Version][badge-release]][release]
[![Software License][badge-license]][license]

## About
This is unofficial PHP wrapper for the ActiveCampaign API v3. For the time being we start with Contact entity only.

## Installation

The preferred method of installation is via [Packagist][] and [Composer][]. Run the following command to install the package and add it as a requirement to your project's `composer.json`:

```bash
composer require papajin/activecampaign-api-php
```

## Requirements
The library uses [Guzzle][] library for http calls. Version ~6.0 is stable version for the moment. 
The PHP version (5.6.38) is used in our environment (due to some legacy code restrictions). The package was also working OK with PHP version 7.2. Operation of the package with other PHP versions not tested.   

## Examples
Please, refer to the [API docs][] for the parameters and responses structure.
```php
<?php
require 'vendor/autoload.php';

use papajin\ActiveCampaign\AC\Contact;
use \GuzzleHttp\Exception\ClientException;


const AC_API_PROJECT = 'https://account.api-us1.com';
const AC_API_KEY = 'somelongstringherewithyourkey';

$ac_contact = Contact::instance( AC_API_PROJECT, AC_API_KEY );

/* OR $contact = Contact::instance(  new \GuzzleHttp\Client( $options ) ); */

$id = 7;

try {

    // Get data for contact with id 7
    $response_body = $ac_contact->show( $id );

    $contact = $response_body->contact;

    $geoIps = $response_body->geoIps; // ...and so on.
    
    // Create contact
    $data = [
        "email"     => "john_doe@gmail.com",
        "firstName" => "John",
        "lastName"  => "Doe",
    ];
    
    $response_body = $ac_contact->create( $data );

} catch ( ClientException $e ) {

    // Something wrong on the service side
    if( 404 == $e->getCode() )
        echo 'Not found exception: ' . $e->getMessage() . PHP_EOL;
    elseif ( 403 == $e->getCode() )
        echo 'Check that valid token provided: ' . $e->getMessage() . PHP_EOL;

}
catch ( RuntimeException $e ) {
 
     // Something wrong on your side
     echo 'Caught exception: ' . $e->getMessage() . PHP_EOL;
 
 }
```
[packagist]: https://packagist.org/packages/papajin/activecampaign-api-php
[composer]: http://getcomposer.org/
[guzzle]: http://docs.guzzlephp.org/en/stable/
[API docs]: https://developers.activecampaign.com/reference

[badge-source]: https://img.shields.io/badge/source-papajin/activecampaign&ndash;api&ndash;php-blue
[badge-release]: https://img.shields.io/packagist/v/papajin/activecampaign-api-php.svg?style=flat-square
[badge-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square

[source]: https://github.com/papajin/activecampaign-api-php
[release]: https://packagist.org/packages/papajin/activecampaign-api-php
[license]: https://github.com/papajin/activecampaign-api-php/LICENSE
