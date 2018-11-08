<?php

namespace Inviqa\Zalando\Api\Writer;

use SplFileObject;

class JsonFileWriterFactory
{
    public static function create(): JsonFileWriter
    {
        return new JsonFileWriter(new SplFileObject('php://memory', 'w+'));
    }
}
