<?php

namespace Clearstream\XmlToArray;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

class XmlToArray
{
    public static function convert(string $xml): array
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

        return [$documentElement->tagName => self::getArrayRepresentation($documentElement)];
    }

    private static function getArrayRepresentation(DOMNode $domNode): array
    {
        $array = [];

        // Here we get all the attributes and prepend the attribute name with "@".
        // `<user id="1" name="John"/>` will result in `['@id' => '1', 'name' => 'John']`.
        foreach (static::getAttributes($domNode) as $attribute) {
            $array['@'.$attribute->nodeName] = $attribute->nodeValue;
        }

        $childNodes = static::getChildNodes($domNode);

        // `<article>Hello<article>` has 1 DOMText child node. It will result
        // in `['#text' => 'Hello']`.
        // `<email></email>` has 0 child nodes, but we still want the value to be
        // present (even if it's an empty string). It will result in `['#text' => '']`.
        $array['#text'] = count($childNodes) === 1 && $childNodes[0] instanceof DOMText
            ? trim($childNodes[0]->textContent)
            : '';

        // Here we process DOMElement child nodes. The results are grouped by the tag name.
        // `<id>1</id><name>Illia</name>` will result in
        // `['id' => [['#text' => '1']], 'name' => [['#text' => 'Illia']]]`
        // `<option>foo</option><option>bar</option>` will result in
        // `['option' => [['#text' => 'foo'], ['#text' => 'bar']]]`
        foreach ($childNodes as $childNode) {
            if ($childNode instanceof DOMElement) {
                $array[$childNode->tagName] ??= [];
                $array[$childNode->tagName][] = self::getArrayRepresentation($childNode);
            }
        }

        return $array;
    }

    /**
     * @param DOMNode $domNode
     * @return DOMNode[]
     */
    private static function getAttributes(DOMNode $domNode): array
    {
        $attributes = [];

        for ($itemIndex = 0; $itemIndex < $domNode->attributes->count(); $itemIndex++) {
            $attributes[] = $domNode->attributes->item($itemIndex);
        }

        return $attributes;
    }

    /**
     * @param DOMNode $domNode
     * @return DOMNode[]
     */
    private static function getChildNodes(DOMNode $domNode): array
    {
        $childNodes = [];

        for ($itemIndex = 0; $itemIndex < $domNode->childNodes->count(); $itemIndex++) {
            $childNodes[] = $domNode->childNodes->item($itemIndex);
        }

        return $childNodes;
    }
}
