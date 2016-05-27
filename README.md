# PhpSpec Matcher Loader Extension

[![Travis](https://img.shields.io/travis/nick-jones/phpspec-matcher-loader-extension.svg?style=flat-square)](https://travis-ci.org/nick-jones/phpspec-matcher-loader-extension)
[![Packagist](https://img.shields.io/packagist/v/nick-jones/phpspec-matcher-loader-extension.svg?style=flat-square)](https://packagist.org/packages/nick-jones/phpspec-matcher-loader-extension)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%205.4-8892BF.svg?style=flat-square)](https://php.net/)

When using [phpspec](https://github.com/phpspec/phpspec) it is possible to create custom matchers by leveraging the
[inline matching](http://www.phpspec.net/en/latest/cookbook/matchers.html#inline-matcher) capability of
`ObjectBehavior`. This, whilst very simple and handy, does have some limitations. Should a particular matcher or set of
matchers be useful in a number of different specifications, the `getMatcher` method must be prepared time and time
again. Of course, you could define some base class for your specifications, but the defined matchers are then are tied
to that class, and are not easily shared, published, or categorised.

This very basic extension allows you to list matchers to be loaded within your `phpspec.yml` configuration file.

## Installation

You can install this extension via [composer](http://getcomposer.org):

`composer require --dev nick-jones/phpspec-matcher-loader-extension`

You will then need to list configure this as an extension within your `phpspec.yml`:

```yaml
extensions:
  - PhpSpecExtension\MatcherLoader\Extension
```

## Usage

You can list any number of custom phpspec matchers under the `matcher` option key within your `phpspec.yml`, for
example:

```yaml
matchers:
  - Acme\Foo\CustomMatcher
  - # etc..
```

All listed matchers must either implement `PhpSpec\Matcher\MatcherInterface` or
`PhpSpec\Matcher\MatchersProviderInterface`. The latter may be familiar, as `ObjectBehavior` implements it. Remember
that the primary difference is that the registered matchers become available in *all* specifications. A matcher example:

```php
<?php

namespace Acme\Foo;

use PhpSpec\Matcher\MatchersProviderInterface;

class JsonMatcher implements MatchersProviderInterface
{
    public function getMatchers()
    {
        return [
            'beValidJson' => function ($subject) {
                json_decode($subject);
                return json_last_error() === 0;
            }
        ];
    }
}
```

### Autoload

If you wish to place your matcher classes under a projects `spec` directory you will need to ensure that they can
be loaded. One way to achieve this is to list them to the
[`autoload-dev.files`](https://getcomposer.org/doc/04-schema.md#files) section of your `composer.json`. For convenience
purposes you could add the entire `spec\<Project>` namespace for autoload
[via PSR-4](https://getcomposer.org/doc/04-schema.md#psr-4). For example:

```json
"autoload-dev": {
  "psr-4": {
    "spec\\Acme\\": "spec/Acme"
  }
}
```
