<?php

namespace Inviqa\Zalando\Api\Writer;

use SplFileObject;

class JsonFileWriter
{
    /**
     * @var SplFileObject
     */
    private $file;

    public function __construct(SplFileObject $file)
    {
        $this->file = $file;
    }

    public function write(array $data): string
    {
        $json = json_encode($data);

        $this->file->fwrite($json);

        return $json;
    }
}
