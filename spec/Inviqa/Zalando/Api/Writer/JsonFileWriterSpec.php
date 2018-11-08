<?php

namespace spec\Inviqa\Zalando\Api\Writer;

use Inviqa\Zalando\Api\Writer\JsonFileWriter;
use PhpSpec\ObjectBehavior;

/**
 * @mixin JsonFileWriter
 */
class JsonFileWriterSpec extends ObjectBehavior
{
    function let(\SplFileObject $file)
    {
        $this->beConstructedWith($file);
    }

    function it_writes_a_JSON_string_to_a_file(\SplFileObject $file)
    {
        $data = ['price' => 34.99];
        $json = '{"price":34.99}';

        $file->fwrite($json)->shouldBeCalled();

        $this->write($data)->shouldReturn($json);
    }
}
