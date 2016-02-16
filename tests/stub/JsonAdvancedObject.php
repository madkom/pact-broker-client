<?php

namespace Madkom\Stub;

/**
 * Class JsonAdvancedObject
 * @package Madkom\Stub
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 */
class JsonAdvancedObject implements \JsonSerializable
{

    /** @var  string */
    private $filePath;

    public function __construct()
    {
        $this->filePath = __DIR__.'/files/advanced-contract.json';
    }

    /**
     * @return array
     */
    function jsonSerialize()
    {
        return json_decode(file_get_contents($this->filePath));
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->filePath;
    }


}