<?php

namespace Clearstream\XmlToArray;

class XmlToArrayConfig
{
    private bool $detachPrefixes = false;

    public function getDetachPrefixes(): bool
    {
        return $this->detachPrefixes;
    }

    public function setDetachPrefixes(bool $detachPrefixes): void
    {
        $this->detachPrefixes = $detachPrefixes;
    }
}
