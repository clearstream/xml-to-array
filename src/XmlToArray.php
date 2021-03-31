<?php

namespace Clearstream\XmlToArray;

class XmlToArray
{
    public static function convert(string $xml, ?XmlToArrayConfig $config = null): array
    {
        $converter = new XmlToArrayConverter($config ?? new XmlToArrayConfig());

        return $converter->convert($xml);
    }
}
