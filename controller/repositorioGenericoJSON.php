<?php

class RepositorioGenericoJSON implements JsonSerializable
{
    protected $arrObj;
    protected $filePath;
    protected $offset;

    public function __construct($filePath, string $offset)
    {
        $this->filePath = $filePath;
        $this->offset   = $offset;
        $this->arrObj   = $this->parseFile($filePath) ?: [$offset => []];
    }

    /**
     * This method parse a JSON file and returns its decoded contents.
     * If the file doesnt exists it creates a new one.
     *
     * @param $filePath of the JSON file
     *
     * @return json_decode(file_get_contents($filePath), true);
     */
    private function parseFile($filePath)
    {
      echo $filePath;
        if (file_exists($filePath))
            return json_decode(file_get_contents($filePath), true);
        else
            fclose(fopen($filePath, 'w+'));
        return json_decode(file_get_contents($filePath), true);
    }

    /**
     * Submits an object that's been serialized with JsonSerializable
     * to the specified JSON file
     *
     * @param $arrObj its an instance from a class that implements JsonSerializable
     * @return amount of bytes written into the file or FALSE in case of error
     */
    protected function submitObject($arrObj)
    {
        array_push($this->arrObj[$this->offset], $arrObj);
        return file_put_contents($this->filePath, json_encode($this->arrObj, JSON_PRETTY_PRINT));
    }

    public function jsonSerialize() { return [$this->offset => []]; }
}