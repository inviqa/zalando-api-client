<?php

namespace Inviqa\Zalando\Api\Security;

use DateTime;
use DateTimeImmutable;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class AuthenticationStorage
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $filePath;

    public function __construct(Filesystem $filesystem, string $filePath)
    {
        $this->filesystem = $filesystem;
        $this->filePath = $filePath;
    }

    public function save(AuthenticationParameters $parameters): void
    {
        $this->ensureDirectoryExists();
        $this->filesystem->dumpFile($this->filePath, Yaml::dump($parameters->toArray()));
    }

    public function fetch(): ?AuthenticationParameters
    {
        if (!$this->filesystem->exists($this->filePath)) {
            return null;
        }

        // @todo Update symfony/yaml to 3.4.x and use Yaml::parseFile here instead
        $parameters = Yaml::parse(file_get_contents($this->filePath), Yaml::PARSE_DATETIME);

        if ($parameters['authenticated_at'] instanceof DateTime) {
            $parameters['authenticated_at'] = DateTimeImmutable::createFromMutable($parameters['authenticated_at']);
        }

        return empty($parameters) ? null : new AuthenticationParameters($parameters);
    }

    private function ensureDirectoryExists(): void
    {
        $directory = pathinfo($this->filePath, PATHINFO_DIRNAME);

        if (!$this->filesystem->exists($directory)) {
            $this->filesystem->mkdir($directory, 0755);
        }
    }
}
