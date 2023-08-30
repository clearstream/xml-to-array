<?php

namespace Clearstream\XmlToArray;

class XmlToArrayConfig
{
    private bool $detachNamespaces = false;

    private bool $trimText = true;

    public function getDetachNamespaces(): bool
    {
        return $this->detachNamespaces;
    }

    public function setDetachNamespaces(bool $detachNamespaces): self
    {
        $this->detachNamespaces = $detachNamespaces;

        return $this;
    }

    public function getTrimText(): bool
    {
        return $this->trimText;
    }

    public function setTrimText(bool $trimText): self
    {
        $this->trimText = $trimText;

        return $this;
    }
}
