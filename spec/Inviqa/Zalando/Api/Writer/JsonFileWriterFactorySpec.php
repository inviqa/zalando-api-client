<?php

namespace spec\Inviqa\Zalando\Api\Writer;

use Inviqa\Zalando\Api\Writer\JsonFileWriter;
use Inviqa\Zalando\Api\Writer\JsonFileWriterFactory;
use PhpSpec\ObjectBehavior;

/**
 * @mixin JsonFileWriterFactory
 */
class JsonFileWriterFactorySpec extends ObjectBehavior
{
    function it_creates_an_in_memory_file_writer()
    {
        $this->create()->shouldReturnAnInstanceOf(JsonFileWriter::class);
    }
}
