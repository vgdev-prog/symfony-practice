<?php

declare (strict_types=1);

namespace App\Utils\Filesystem;

use Symfony\Component\Filesystem\Filesystem;

readonly class FilesystemWorker
{
    public function __construct(
        private Filesystem $filesystem,
    )
    {
    }

    public function createFolderIfItNotExist(string $folder):void
    {
        if (!$this->filesystem->exists($folder)) {
            $this->filesystem->mkdir($folder);
        }
    }
}
