## Install

First, run

    git clone git@github.com:ibuildingsnl/coding-dojo.git

Inside the `coding-dojo` directory, run

    composer install

(assuming [you have Composer installed](https://getcomposer.org/download/) globally).

## Getting started

Put your classes in `src/`, your PHPUnit tests in `test/`.

The root namespace in `src/` is `CodingDojo`, in 'test/' it's `CodingDojo\Test`.

To run the unit tests:

    vendor/bin/phpunit
