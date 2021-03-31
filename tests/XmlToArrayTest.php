<?php

namespace Clearstream\XmlToArray\Tests;

use Clearstream\XmlToArray\XmlToArray;
use Clearstream\XmlToArray\XmlToArrayConfig;
use PHPUnit\Framework\TestCase;

class XmlToArrayTest extends TestCase
{
    /** @test */
    public function covert_method_works_with_default_config()
    {
        $this->assertSame(
            ['root' => ['#text' => '']],
            XmlToArray::convert('<root />')
        );
    }

    /** @test */
    public function covert_method_works_with_custom_config()
    {
        $config = new XmlToArrayConfig();
        $config->setDetachNamespaces(true);

        $this->assertSame(
            ['root' => ['#namespace' => 'foo', '#text' => '']],
            XmlToArray::convert('<foo:root xmlns:foo="http://example.com/foo" />', $config)
        );
    }
}
