<?php

namespace Clearstream\XmlToArray\Tests;

use Clearstream\XmlToArray\XmlToArrayConfig;
use PHPUnit\Framework\TestCase;

class XmlToArrayConfigTest extends TestCase
{
    /** @test */
    public function detach_namespace()
    {
        $this->assertFalse((new XmlToArrayConfig())->getDetachNamespaces());
        $this->assertFalse((new XmlToArrayConfig())->setDetachNamespaces(false)->getDetachNamespaces());
        $this->assertTrue((new XmlToArrayConfig())->setDetachNamespaces(true)->getDetachNamespaces());
    }
}
