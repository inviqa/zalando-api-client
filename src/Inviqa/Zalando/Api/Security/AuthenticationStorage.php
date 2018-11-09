<?php

namespace Inviqa\Zalando\Api\Security;

use Inviqa\Zalando\Api\Configuration;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class AuthenticationStorage
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $filePath;

    public function __construct(Filesystem $filesystem, Configuration $configuration)
    {
        $this->filesystem = $filesystem;
        $this->filePath = $configuration->getAuthenticationConfigFilePath();
    }

    public function save(array $data): void
    {
        $this->ensureDirectoryExists();
        $this->filesystem->dumpFile($this->filePath, Yaml::dump($data));
    }

    public function fetch(): array
    {
        $data = null;

        if ($this->filesystem->exists($this->filePath)) {
            $data = Yaml::parseFile($this->filePath, Yaml::PARSE_DATETIME);
        }

        return empty($data) ? [] : $data;
    }

    private function ensureDirectoryExists(): void
    {
        $directory = pathinfo($this->filePath, PATHINFO_DIRNAME);

        if (!$this->filesystem->exists($directory)) {
            $this->filesystem->mkdir($directory, 0755);
        }
    }
}
