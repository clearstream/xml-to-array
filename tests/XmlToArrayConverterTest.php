<?php

namespace Clearstream\XmlToArray\Tests;

use Clearstream\XmlToArray\XmlToArray;
use Clearstream\XmlToArray\XmlToArrayConfig;
use Clearstream\XmlToArray\XmlToArrayConverter;
use Clearstream\XmlToArray\XmlToArrayException;
use PHPUnit\Framework\TestCase;

class XmlToArrayConverterTest extends TestCase
{
    /** @test */
    public function handles_attributes_correctly()
    {
        $xml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<response success="true">
    <user id="1" name="Illia" />
</response>
XML;

        $expected = [
            'response' => [
                '@success' => 'true',
                '#text' => '',
                'user' => [
                    [
                        '@id' => '1',
                        '@name' => 'Illia',
                        '#text' => '',
                    ],
                ],
            ],
        ];

        $config = new XmlToArrayConfig();

        $this->assertSame($expected, (new XmlToArrayConverter($config))->convert($xml));
    }

    /** @test */
    public function handles_multiple_nodes_with_same_name_correctly()
    {
        $xml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<response>
    <users>
        <user id="1" name="Illia" />
        <user id="2" name="Trevor" />
    </users>
</response>
XML;

        $expected = [
            'response' => [
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
            ],
        ];

        $config = new XmlToArrayConfig();

        $this->assertSame($expected, (new XmlToArrayConverter($config))->convert($xml));
    }

    /** @test */
    public function handles_nodes_with_text_correctly()
    {
        $xml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<root>
    <name>
        Illia
    </name>
</root>
XML;

        $expected = [
            'root' => [
                '#text' => '',
                'name' => [
                    [
                        '#text' => 'Illia',
                    ],
                ],
            ],
        ];

        $config = new XmlToArrayConfig();

        $this->assertSame($expected, (new XmlToArrayConverter($config))->convert($xml));
    }

    /** @test */
    public function handles_nodes_with_text_and_non_text_nodes_correctly()
    {
        $xml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<root>
    <question>
        How many circles are there on this image?
        <img src="/images/circles.png"></img>
        Reply with number.
    </question>
</root>
XML;

        $expected = [
            'root' => [
                '#text' => '',
                'question' => [
                    [
                        '#text' => 'How many circles are there on this image?Reply with number.',
                        'img' => [
                            [
                                '@src' => '/images/circles.png',
                                '#text' => '',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $config = new XmlToArrayConfig();

        $this->assertSame($expected, (new XmlToArrayConverter($config))->convert($xml));
    }

    /** @test */
    public function handles_empty_nodes_correctly()
    {
        $xml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<root>
    <article />
</root>
XML;

        $expected = [
            'root' => [
                '#text' => '',
                'article' => [
                    [
                        '#text' => '',
                    ],
                ],
            ],
        ];

        $config = new XmlToArrayConfig();

        $this->assertSame($expected, (new XmlToArrayConverter($config))->convert($xml));
    }

    /** @test */
    public function handles_nodes_with_cdata_correctly()
    {
        $xml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<root>
    <article><![CDATA[You can use <b>this tag</b> to display the bold text.]]></article>
</root>
XML;

        $expected = [
            'root' => [
                '#text' => '',
                'article' => [
                    [
                        '#text' => 'You can use <b>this tag</b> to display the bold text.',
                    ],
                ],
            ],
        ];

        $config = new XmlToArrayConfig();

        $this->assertSame($expected, (new XmlToArrayConverter($config))->convert($xml));
    }

    /** @test */
    public function handles_namespaced_nodes_correctly()
    {
        $xml = <<<'XML'
<test:root xmlns:foo="http://example.com/foo" xmlns:bar="http://example.com/bar" xmlns:test="http://example.com/test">
    <foo:item>
        A
    </foo:item>
    <foo:item>
        B
    </foo:item>
    <bar:item>
        C
    </bar:item>
</test:root>
XML;

        $expected = [
            'test:root' => [
                '#text' => '',
                'foo:item' => [
                    [
                        '#text' => 'A',
                    ],
                    [
                        '#text' => 'B',
                    ],
                ],
                'bar:item' => [
                    [
                        '#text' => 'C',
                    ],
                ],
            ],
        ];

        $config = new XmlToArrayConfig();

        $this->assertSame($expected, (new XmlToArrayConverter($config))->convert($xml));
    }

    /** @test */
    public function does_not_trim_values_when_trim_text_is_false(): void
    {
        $xml = <<<'XML'
<test:root xmlns:foo="http://example.com/foo" xmlns:bar="http://example.com/bar" xmlns:test="http://example.com/test">
    <foo> B </foo>
</test:root>
XML;

        $expected = [
            'test:root' => [
                '#text' => PHP_EOL.'    '.PHP_EOL,
                'foo' => [
                    [
                        '#text' => ' B ',
                    ],
                ],
            ],
        ];

        $config = new XmlToArrayConfig();
        $config->setTrimText(false);

        $this->assertSame($expected, (new XmlToArrayConverter($config))->convert($xml));
    }

    /** @test */
    public function trims_values_when_trim_text_is_true(): void
    {
        $xml = <<<'XML'
<test:root xmlns:foo="http://example.com/foo" xmlns:bar="http://example.com/bar" xmlns:test="http://example.com/test">
    <foo> B </foo>
</test:root>
XML;

        $expected = [
            'test:root' => [
                '#text' => '',
                'foo' => [
                    [
                        '#text' => 'B',
                    ],
                ],
            ],
        ];

        $config = new XmlToArrayConfig();
        $config->setTrimText(true);

        $this->assertSame($expected, (new XmlToArrayConverter($config))->convert($xml));
    }

    /** @test */
    public function handles_namespaced_nodes_correctly_when_detach_namespaces_is_true()
    {
        $xml = <<<'XML'
<test:root xmlns:foo="http://example.com/foo" xmlns:bar="http://example.com/bar" xmlns:test="http://example.com/test">
    <foo:item>
        A
    </foo:item>
    <foo:item>
        B
    </foo:item>
    <bar:item>
        C
    </bar:item>
</test:root>
XML;

        $expected = [
            'root' => [
                '#namespace' => 'test',
                '#text' => '',
                'item' => [
                    [
                        '#namespace' => 'foo',
                        '#text' => 'A',
                    ],
                    [
                        '#namespace' => 'foo',
                        '#text' => 'B',
                    ],
                    [
                        '#namespace' => 'bar',
                        '#text' => 'C',
                    ],
                ],
            ],
        ];

        $config = new XmlToArrayConfig();
        $config->setDetachNamespaces(true);

        $this->assertSame($expected, (new XmlToArrayConverter($config))->convert($xml));
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