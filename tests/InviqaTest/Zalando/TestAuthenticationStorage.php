<?php

namespace InviqaTest\Zalando;

use Inviqa\Zalando\Api\Security\AuthenticationStorage;

class TestAuthenticationStorage extends AuthenticationStorage
{
    public function delete(): void
    {
        $this->filesystem->remove($this->filePath);
    }

    public function fileExists(): bool
    {
        return $this->filesystem->exists($this->filePath);
    }
}
