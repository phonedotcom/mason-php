## Mason PHP

[![Build Status](https://travis-ci.org/Phone-com/mason-php.svg?branch=master)](https://travis-ci.org/Phone-com/mason-php)
[![Latest Stable Version](https://poser.pugx.org/phonecom/mason-php/v/stable)](https://packagist.org/packages/phonecom/mason-php)
[![Total Downloads](https://poser.pugx.org/phonecom/mason-php/downloads)](https://packagist.org/packages/phonecom/mason-php)
[![Latest Unstable Version](https://poser.pugx.org/phonecom/mason-php/v/unstable)](https://packagist.org/packages/phonecom/mason-php)
[![License](https://poser.pugx.org/phonecom/mason-php/license)](https://packagist.org/packages/phonecom/mason-php)

This project provides a PHP tool for building Hypermedia REST API's in [Mason](https://github.com/JornWildt/Mason) format.

At present, it includes a set of Mason Builder classes for producing data structures that can be passed to `json_encode()` for rendering Mason response bodies.

## Example

Here is a simple example:

```
<?php
use PhoneCom\Mason\Builder\Document;

$doc = new Document([
    'first_name' => 'Oscar',
    'last_name' => 'Grouch',
    'birthday' => 'Apr 1',
    'address' => 'Sesame Street'
]);

$doc->addMetaProperty('@title', 'Oscar the Grouch')
    ->setProperty('clothing', 'Trash can')
    ->setControl('self', 'http://example.com/characters/oscar');

echo json_encode($doc);
```

This produces the following data structure:

```
{
    "first_name": "Oscar",
    "last_name": "Grouch",
    "birthday": "Apr 1",
    "address": "Sesame Street",
    "clothing": "Trash can",
    "@meta": {
        "@title": "Oscar the Grouch"
    },
    "@controls": {
        "self": {
            "href": "http://example.com/characters/oscar"
        }
    }
}
```

## Motivation

[Mason](https://github.com/JornWildt/Mason) is a Hypermedia RESTful API format first published in 2013 by [Jørn Wildt](https://github.com/JornWildt). It combines lessons learned from years of API implementations and close study of other competing formats such as Hal, Collection+JSON, and JSON-LD.  Each format has its strengths, but none has the desired balance of simplicity and features.

Mason-PHP was built to help promote Mason adoption within the PHP community, and to provide much needed tooling.

## Installation

This project has no runtime dependencies. If you want to run the tests or write more, you will need to run `composer install`.

TODO (Better installation coming soon via Composer/Packagist)

## Usage

Documentation is forthcoming. All public methods are documented inline, so at the moment, the best way to learn is to browse the code.

## Tests

Run `phpunit` in the top folder.  If you have the `XDebug` extension installed, you may also run it with the Code Coverage config which is provided separately, e.g. `phpunit -c phpunit-coverage-html.xml`

## Contributors
This project was created and is managed by [Phone.com](https://www.phone.com). We're building a hot new Hypermedia API and we chose Mason!

Pull requests are welcome.

## License

This project is released under the MIT License. Copyright (c) 2015 [Phone.com, Inc.](https://www.phone.com) See `LICENSE` for full details.
