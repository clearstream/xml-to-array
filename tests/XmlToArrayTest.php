<?php

namespace Clearstream\XmlToArray\Tests;

use Clearstream\XmlToArray\XmlToArray;
use Clearstream\XmlToArray\XmlToArrayException;
use PHPUnit\Framework\TestCase;

class XmlToArrayTest extends TestCase
{
    /** @test */
    public function converts_correctly()
    {
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

        $expected = [
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
        ];

        $this->assertSame($expected, XmlToArray::convert($xml));
    }

    /**
     * @test
     * @dataProvider invalid_input_data_provider
     * @param string $invalidInput
     * @throws XmlToArrayException
     */
    public function throws_an_exception_if_invalid_input_provided(string $invalidInput)
    {
        $this->expectException(XmlToArrayException::class);

        XmlToArray::convert($invalidInput);
    }

    public function invalid_input_data_provider(): array
    {
        return [
            [''],
            ['invalid input'],
            ['<root></root><another-root></another-root>'],
        ];
    }
}