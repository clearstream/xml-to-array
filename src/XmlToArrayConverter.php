<?php

namespace Clearstream\XmlToArray;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

class XmlToArrayConverter
{
    private XmlToArrayConfig $config;

    public function __construct(XmlToArrayConfig $config)
    {
        $this->config = $config;
    }

    public function convert(string $xml): array
    {
        // Here we throw an exception for empty XML string to avoid
        // `ErrorException` when calling `$domDocument->loadXML()`.
        if ($xml === '') {
            throw new XmlToArrayException;
        }

        // Here we get the value that represents whether the
        // internal libxml errors should be used.
        $libxmlUseInternalErrors = libxml_use_internal_errors();

        // Here we enable the internal libxml errors.
        libxml_use_internal_errors(true);
        // And clear existing errors just in case there are some.
        libxml_clear_errors();

        $domDocument = new DOMDocument;
        $domDocument->loadXML($xml);

        // Here get the last libxml error. In case the value
        // is not null, it means that the XML parsing failed.
        $libxmlLastError = libxml_get_last_error();

        // Here we roll back the initial value, because we
        // don't want this method to affect the global state.
        libxml_use_internal_errors($libxmlUseInternalErrors);

        // In case there was a parsing error, we throw an exception.
        if ($libxmlLastError) {
            throw new XmlToArrayException;
        }

        $documentElement = $domDocument->documentElement;

        return [$this->getElementName($documentElement) => self::getArrayRepresentation($documentElement)];
    }

    private function getArrayRepresentation(DOMElement $domElement): array
    {
        $array = [];

        // Here we get all the attributes and prepend the attribute name with "@".
        // `<user id="1" name="John"/>` will result in `['@id' => '1', 'name' => 'John']`.
        foreach (static::getElementAttributes($domElement) as $attribute) {
            $array['@'.$attribute->nodeName] = $attribute->nodeValue;
        }

        if ($this->config->getDetachNamespaces()) {
            $array['#namespace'] = $domElement->prefix;
        }

        $childNodes = static::getElementChildNodes($domElement);

        $array['#text'] = implode('', array_map(function (DOMNode $childNode) {
            if ($childNode instanceof DOMText) {
                return $this->config->getTrimText() ? trim($childNode->textContent) : $childNode->textContent;
            }

            return '';
        }, $childNodes));

        // Here we process DOMElement child nodes. The results are grouped by the tag name.
        // `<id>1</id><name>Illia</name>` will result in
        // `['id' => [['#text' => '1']], 'name' => [['#text' => 'Illia']]]`
        // `<option>foo</option><option>bar</option>` will result in
        // `['option' => [['#text' => 'foo'], ['#text' => 'bar']]]`
        foreach ($childNodes as $childNode) {
            if ($childNode instanceof DOMElement) {
                $array[$this->getElementName($childNode)] ??= [];
                $array[$this->getElementName($childNode)][] = self::getArrayRepresentation($childNode);
            }
        }

        return $array;
    }

    private function getElementName(DOMElement $domElement): string
    {
        return $this->config->getDetachNamespaces()
            ? $domElement->localName
            : $domElement->tagName;
    }

    /**
     * @param DOMElement $domElement
     * @return DOMNode[]
     */
    private function getElementAttributes(DOMElement $domElement): array
    {
        $attributes = [];

        for ($itemIndex = 0; $itemIndex < $domElement->attributes->count(); $itemIndex++) {
            $attributes[] = $domElement->attributes->item($itemIndex);
        }

        return $attributes;
    }

    /**
     * @param DOMElement $domElement
     * @return DOMNode[]
     */
    private function getElementChildNodes(DOMElement $domElement): array
    {
        $childNodes = [];

        for ($itemIndex = 0; $itemIndex < $domElement->childNodes->count(); $itemIndex++) {
            $childNodes[] = $domElement->childNodes->item($itemIndex);
        }

        return $childNodes;
    }
}