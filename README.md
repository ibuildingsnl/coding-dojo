## Install

First, run

    git clone git@github.com:ibuildingsnl/coding-dojo.git

Inside the `coding-dojo` directory, run

    git checkout kata/game-of-life-step-2
    composer install

(assuming [you have Composer installed](https://getcomposer.org/download/) globally).

## Getting started

Read the [GameOfLife kata](kata/GameOfLife.md). This branch includes a working Game of Life engine, completing step 1.

Put your classes in `src/`, your PHPUnit tests in `test/`. A learning test has been provided that shows how to use Igor
Wiedler's Game of Life implementation.

The root namespace in `src/` is `CodingDojo`, in 'test/' it's `CodingDojo\Test`.

To run the unit tests:

    vendor/bin/phpunit
