# PhpSpec Matcher Loader Extension

[![Build Status](https://travis-ci.org/nick-jones/phpspec-matcher-loader-extension.svg?branch=master)](https://travis-ci.org/nick-jones/phpspec-matcher-loader-extension)

When using [phpspec](https://github.com/phpspec/phpspec) it is possible to create custom matchers by leveraging the
[inline matching](http://www.phpspec.net/en/latest/cookbook/matchers.html#inline-matcher) capability of
`ObjectBehavior`. This, whilst very simple and handy, does have some limitations. Should a particular matcher or set of
matchers be useful in a number of different specifications, the `getMatcher` method must be prepared time and time
again. Of course, you could define some base class for your specifications, but the defined matchers are then are tied
to that class, and are not easily shared, published, or categorised.

This very basic extension allows you to list matchers to be loaded within your `phpspec.yml` configuration file.

## Installation

You can install this extension via [composer](http://getcomposer.org):

`composer require nick-jones/phpspec-matcher-loader-extension`

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