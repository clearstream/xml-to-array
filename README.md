# XML to array PHP converter

This package provides a very simple class to convert XML string to array.

**Why should I use this package?**

- because you don’t like XML
- because other packages generate inconsistent array structure
- because you can’t serialize SimpleXML objects
- because you just want to use arrays

## Install

You can install this package via composer.

``` bash
composer require clearstream/xml-to-array
```

## Usage

```php
use Clearstream\XmlToArray\XmlToArray;

$xml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<response success="true">
    <users>
        <user id="1" name="Illia" />
        <user id="2" name="Trevor" />
    </users>
    <settings>
        <mode value="light" />
        <color value="purple" />
    </settings>
    <article id="1">
        Hello World!
    </article>
    <empty></empty>
    <cdata><![CDATA[<sender>John Doe</sender>]]></cdata>
</response>
XML;

$array = XmlToArray::convert($xml);
```
After running this piece of code `$array` will contain:

```php
[
    'response' => [
        '@success' => 'true',
        '#text' => '',
        'users' => [
            [
                '#text' => '',
                'user' => [
                    [
                        '@id' => '1',
                        '@name' => 'Illia',
                        '#text' => '',
                    ],
                    [
                        '@id' => '2',
                        '@name' => 'Trevor',
                        '#text' => '',
                    ],
                ],
            ],
        ],
        'settings' => [
            [
                '#text' => '',
                'mode' => [
                    [
                        '@value' => 'light',
                        '#text' => '',
                    ],
                ],
                'color' => [
                    [
                        '@value' => 'purple',
                        '#text' => '',
                    ],
                ],
            ],
        ],
        'article' => [
            [
                '@id' => '1',
                '#text' => 'Hello World!',
            ],
        ],
        'empty' => [
            ['#text' => ''],
        ],
        'cdata' => [
            ['#text' => '<sender>John Doe</sender>'],
        ],
    ],
]
```

If your input contains something that cannot be parsed a `\Clearstream\XmlToArray\XmlToArrayException` will be thrown.

## Testing

```bash
vendor/bin/phpunit
```

## License

GNU GENERAL PUBLIC LICENSE. Please see [License File](LICENSE) for more information.
