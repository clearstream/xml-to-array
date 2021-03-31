<?php

namespace Clearstream\XmlToArray;

class XmlToArrayConfig
{
    private bool $detachNamespaces = false;

    public function getDetachNamespaces(): bool
    {
        return $this->detachNamespaces;
    }

    public function setDetachNamespaces(bool $detachNamespaces): self
    {
        $this->detachNamespaces = $detachNamespaces;

        return $this;
    }
}
