<?php

namespace Madkom\Stub;

/**
 * Created by PhpStorm.
 * User: dgafka
 * Date: 16.02.16
 * Time: 09:00
 */
class JsonSimpleObject implements \JsonSerializable
{

    /** @var  string */
    private $filePath;

    public function __construct()
    {
        $this->filePath = __DIR__.'/files/simple-contract.json';
    }

    /**
     * @return array
     */
    public function jsonSerialize()
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