<?php

namespace Clearstream\XmlToArray;

class XmlToArrayConfig
{
    private bool $detachNamespaces = false;

    private array $trimExcept = [];

    public function getDetachNamespaces(): bool
    {
        return $this->detachNamespaces;
    }

    public function setDetachNamespaces(bool $detachNamespaces): self
    {
        $this->detachNamespaces = $detachNamespaces;

        return $this;
    }

    public function getTrimExcept(): array
    {
        return $this->trimExcept;
    }

    public function setTrimExcept(array $except): self
    {
        $this->trimExcept = $except;

        return $this;
    }
}
