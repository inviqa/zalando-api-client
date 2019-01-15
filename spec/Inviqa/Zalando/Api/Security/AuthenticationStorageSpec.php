<?php

namespace spec\Inviqa\Zalando\Api\Security;

use DateTimeImmutable;
use Inviqa\Zalando\Api\Security\AuthenticationParameters;
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
    function let(Filesystem $filesystem)
    {
        $this->beConstructedWith($filesystem, 'app/config/authentication.yml');
    }

    function it_creates_the_directory_and_saves_the_authentication_parameters(Filesystem $filesystem)
    {
        $data = new AuthenticationParameters([
            'authenticated_at' => new DateTimeImmutable('2019-01-07 15:47:53'),
            'access_token' => 'abc123',
            'expires_in' => 7200,
        ]);
        $yaml = "authenticated_at: 2019-01-07T15:47:53+00:00\naccess_token: abc123\nexpires_in: 7200\n";

        $filesystem->exists('app/config')->willReturn(false);
        $filesystem->mkdir('app/config', 0755)->shouldBeCalled();
        $filesystem->dumpFile('app/config/authentication.yml', $yaml)->shouldBeCalled();

        $this->save($data);
    }

    function it_saves_the_authentication_parameters_when_the_directory_already_exists(Filesystem $filesystem)
    {
        $data = new AuthenticationParameters([
            'authenticated_at' => new DateTimeImmutable('2019-01-07 15:47:53'),
            'access_token' => 'abc123',
            'expires_in' => 7200,
        ]);
        $yaml = "authenticated_at: 2019-01-07T15:47:53+00:00\naccess_token: abc123\nexpires_in: 7200\n";

        $filesystem->exists('app/config')->willReturn(true);
        $filesystem->mkdir(Argument::cetera())->shouldNotBeCalled();
        $filesystem->dumpFile('app/config/authentication.yml', $yaml)->shouldBeCalled();

        $this->save($data);
    }

    function it_fetches_the_authentication_parameters_from_a_file(Filesystem $filesystem)
    {
        $yaml = "authenticated_at: 2019-01-07T15:47:53+00:00\naccess_token: abc123\nexpires_in: 7200\n";
        $authenticatedAt = new DateTimeImmutable('2019-01-07 15:47:53');

        $filesystem->exists('app/config/authentication.yml')->willReturn(true);

        $phpProphet = new PHPProphet();
        $storage = $phpProphet->prophesize('Inviqa\Zalando\Api\Security');
        $storage->file_get_contents(Argument::cetera())->willReturn($yaml);
        $storage->reveal();

        /** @var AuthenticationParameters $result */
        $result = $this->fetch();

        $result->getAuthenticatedAt()->shouldBeLike($authenticatedAt);
        $result->getAccessToken()->shouldReturn('abc123');
        $result->getExpiresIn()->shouldReturn(7200);

        $phpProphet->checkPredictions();
    }

    function it_returns_null_if_the_authentication_parameters_file_is_empty(Filesystem $filesystem)
    {
        $filesystem->exists('app/config/authentication.yml')->willReturn(true);

        $phpProphet = new PHPProphet();
        $storage = $phpProphet->prophesize('Inviqa\Zalando\Api\Security');
        $storage->file_get_contents(Argument::cetera())->willReturn('');
        $storage->reveal();

        $this->fetch()->shouldReturn(null);

        $phpProphet->checkPredictions();
    }

    function it_throws_an_exception_if_the_authentication_parameters_file_contains_an_error(Filesystem $filesystem)
    {
        $yaml = "authenticated_at: 2019-01-07T15:47:53+00:00\naccess_token: abc123\nexpires_in: 7200\nblah";

        $filesystem->exists('app/config/authentication.yml')->willReturn(true);

        $phpProphet = new PHPProphet();
        $storage = $phpProphet->prophesize('Inviqa\Zalando\Api\Security');
        $storage->file_get_contents(Argument::cetera())->willReturn($yaml);
        $storage->reveal();

        $this->shouldThrow(ParseException::class)->duringFetch();

        $phpProphet->checkPredictions();
    }

    function it_returns_null_if_the_authentication_parameters_file_does_not_exist(Filesystem $filesystem)
    {
        $filesystem->exists('app/config/authentication.yml')->willReturn(false);

        $this->fetch()->shouldReturn(null);
    }
}
