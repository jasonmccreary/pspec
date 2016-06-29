# PSpec
PSpec is a modern, BDD-style testing framework for PHP - enabling you to write more readable tests using its simple, conversational DSL and expressive, fluent assertions.

```php
describe('string functions', function () {
    describe('strtolower', function () {
        it('should return a string with all its characters lower case', function () {
            expect(strtolower('PSpec'))->toEqual('pspec');
        });
    });

    describe('strpos', function () {
        context('when needle is found', function () {
            it('should return the first character index of needle within haystack', function () {
                expect(strpos('PSpec', 'Spec'))->toBe(1);
            });
        });

        context('when needle is not found', function () {
            it('should return false', function () {
                expect(strpos('PSpec', 'PHP'))->toBeFalse();
            });
        });
    });
});
```

## Installation

Install PSpec as a **development** dependency to your project using [Composer](https://getcomposer.org):

```sh
composer require --dev pureconcepts/pspec
```

## Usage

Run your tests using the `pspec` command:

```sh
vendor\bin\spec
```

## Documentation

Documentation and additional examples will be available in the official release.

## License

PSpec is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).

## Thanks

PSpec was built atop [matura](https://github.com/jacobstr/matura) and heavily inspired by [RSpec](https://github.com/rspec/rspec-core) and [Jasmine](https://github.com/jasmine/jasmine). I want to recognize and thank these projects.
