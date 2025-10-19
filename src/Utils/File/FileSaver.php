<?php

declare (strict_types=1);

namespace App\Utils\File;

use App\Exception\FileUploadException;
use App\Utils\Filesystem\FilesystemWorker;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileSaver
{
    public function __construct(
        private SluggerInterface $slugger,
        private string           $uploadsTempDir,
        private FilesystemWorker $filesystemWorker,
    )
    {
    }

    public function saveUploadedFileIntoTemp(UploadedFile $uploadedFile): ?string
    {
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $saveFilename = $this->slugger->slug($originalFilename);
        $filename = sprintf('%s_%s.%s', $saveFilename, uniqid('', true), $uploadedFile->guessExtension());
        $this->filesystemWorker->createFolderIfItNotExist($this->uploadsTempDir);

        try {
            $uploadedFile->move($this->uploadsTempDir, $filename);
        } catch (FileUploadException $exception) {

            throw new FileUploadException(
                sprintf('Failed to upload file "%s"', $uploadedFile->getClientOriginalName())
            );
        }

        return $filename;
    }

}
