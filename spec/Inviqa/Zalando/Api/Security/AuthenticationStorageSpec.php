<?php

namespace spec\Inviqa\Zalando\Api\Security;

use DateTime;
use DateTimeImmutable;
use Inviqa\Zalando\Api\Configuration;
use Inviqa\Zalando\Api\Security\AuthenticationStorage;
use phpmock\prophecy\PHPProphet;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * @mixin AuthenticationStorage
 */
class AuthenticationStorageSpec extends ObjectBehavior
{
    function let(Filesystem $filesystem, Configuration $configuration)
    {
        $configuration->getAuthenticationConfigFilePath()->willReturn('app/config/zalando_authentication.yml');

        $this->beConstructedWith($filesystem, $configuration);
    }

    function it_creates_the_directory_and_saves_the_authenticated_data(Filesystem $filesystem)
    {
        $data = [
            'authenticated_at' => new DateTimeImmutable('2019-01-07 15:47:53'),
            'access_token' => 'abc123',
            'expires_in' => 7200,
        ];
        $yaml = "authenticated_at: 2019-01-07T15:47:53+00:00\naccess_token: abc123\nexpires_in: 7200\n";

        $filesystem->exists('app/config')->willReturn(false);
        $filesystem->mkdir('app/config', 0755)->shouldBeCalled();
        $filesystem->dumpFile('app/config/zalando_authentication.yml', $yaml)->shouldBeCalled();

        $this->save($data);
    }

    function it_saves_the_authenticated_data_when_the_directory_already_exists(Filesystem $filesystem)
    {
        $data = [
            'authenticated_at' => new DateTimeImmutable('2019-01-07 15:47:53'),
            'access_token' => 'abc123',
            'expires_in' => 7200,
        ];
        $yaml = "authenticated_at: 2019-01-07T15:47:53+00:00\naccess_token: abc123\nexpires_in: 7200\n";

        $filesystem->exists('app/config')->willReturn(true);
        $filesystem->mkdir(Argument::cetera())->shouldNotBeCalled();
        $filesystem->dumpFile('app/config/zalando_authentication.yml', $yaml)->shouldBeCalled();

        $this->save($data);
    }

    function it_fetches_the_authenticated_data_from_a_configuration_file(Filesystem $filesystem)
    {
        $yaml = "authenticated_at: 2019-01-07T15:47:53+00:00\naccess_token: abc123\nexpires_in: 7200\n";
        $data = [
            'authenticated_at' => new DateTime('2019-01-07 15:47:53'),
            'access_token' => 'abc123',
            'expires_in' => 7200,
        ];

        $filesystem->exists('app/config/zalando_authentication.yml')->willReturn(true);

        $phpProphet = new PHPProphet();
        $parser = $phpProphet->prophesize('Symfony\Component\Yaml');
        $parser->is_file(Argument::cetera())->willReturn(true);
        $parser->is_readable(Argument::cetera())->willReturn(true);
        $parser->file_get_contents(Argument::cetera())->willReturn($yaml);
        $parser->reveal();

        $result = $this->fetch();

        $result->shouldHaveCount(3);
        $result->shouldIterateLike($data);
        $result->shouldHaveKeyWithValue('access_token', 'abc123');
        $result->shouldHaveKeyWithValue('expires_in', 7200);

        $phpProphet->checkPredictions();
    }

    function it_returns_an_empty_array_if_the_configuration_file_is_empty(Filesystem $filesystem)
    {
        $filesystem->exists('app/config/zalando_authentication.yml')->willReturn(true);

        $phpProphet = new PHPProphet();
        $parser = $phpProphet->prophesize('Symfony\Component\Yaml');
        $parser->is_file(Argument::cetera())->willReturn(true);
        $parser->is_readable(Argument::cetera())->willReturn(true);
        $parser->file_get_contents(Argument::cetera())->willReturn('');
        $parser->reveal();

        $this->fetch()->shouldReturn([]);

        $phpProphet->checkPredictions();
    }

    function it_throws_an_exception_if_the_configuration_file_contains_an_error(Filesystem $filesystem)
    {
        $yaml = "authenticated_at: 2019-01-07T15:47:53+00:00\naccess_token: abc123\nexpires_in: 7200\nblah";

        $filesystem->exists('app/config/zalando_authentication.yml')->willReturn(true);

        $phpProphet = new PHPProphet();
        $parser = $phpProphet->prophesize('Symfony\Component\Yaml');
        $parser->is_file(Argument::cetera())->willReturn(true);
        $parser->is_readable(Argument::cetera())->willReturn(true);
        $parser->file_get_contents(Argument::cetera())->willReturn($yaml);
        $parser->reveal();

        $this->shouldThrow(ParseException::class)->duringFetch();

        $phpProphet->checkPredictions();
    }

    function it_returns_an_empty_array_if_the_configuration_file_does_not_exist(Filesystem $filesystem)
    {
        $filesystem->exists('app/config/zalando_authentication.yml')->willReturn(false);

        $this->fetch()->shouldReturn([]);
    }
}
