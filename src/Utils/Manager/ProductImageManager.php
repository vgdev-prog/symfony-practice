<?php

declare(strict_types=1);

namespace App\Utils\Manager;

use App\Entity\ProductImage;
use App\Utils\File\FileImageResizer;
use App\Utils\Filesystem\FilesystemWorker;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class ProductImageManager extends AbstractBaseManager
{
    public function __construct(
        EntityManagerInterface $entityManager,
        private FilesystemWorker       $filesystemWorker,
        private FileImageResizer       $fileImageResizer,
        private string                 $uploadsTempDir
    )
    {
        parent::__construct($entityManager);
    }

    public function saveImageForProduct(string $productDir, string $tempImageFilename)
    {
        if (!$tempImageFilename) {
            return null;
        }

        $this->filesystemWorker->createFolderIfItNotExist($productDir);
        $filenameId = uniqid();
        $imageSmallParams = [
            'width' => 60,
            'height' => null,
            'newFolder' => $productDir,
            'newFilename' => sprintf('%s_%s.jpg', $filenameId, 'small'),
        ];
        $imageSmall = $this->fileImageResizer->resizeImageAndSave($this->uploadsTempDir, $tempImageFilename, $imageSmallParams);

        $imageMiddleParams = [
            'width' => 430,
            'height' => null,
            'newFolder' => $productDir,
            'newFilename' => sprintf('%s_%s.jpg', $filenameId, 'medium'),
        ];
        $imageMedium =  $this->fileImageResizer->resizeImageAndSave($this->uploadsTempDir, $tempImageFilename, $imageMiddleParams);;

        $imageBigParams = [
            'width' => 800,
            'height' => null,
            'newFolder' => $productDir,
            'newFilename' => sprintf('%s_%s.jpg', $filenameId, 'big'),
        ];
        $imageBig =  $this->fileImageResizer->resizeImageAndSave($this->uploadsTempDir, $tempImageFilename, $imageBigParams);

        $productImage = new ProductImage();
        $productImage->setFilenameSmall($imageSmall);
        $productImage->setFilenameMiddle($imageMedium);
        $productImage->setFilenameBig($imageBig);

        return $productImage;
    }

    public function removeImageFromProduct(ProductImage $productImage, string $productImagesDir): void
    {
        $smallFilePath = $productImagesDir . DIRECTORY_SEPARATOR . $productImage->getFilenameSmall();
        $this->filesystemWorker->remove($smallFilePath);

        $mediumFilePath = $productImagesDir . DIRECTORY_SEPARATOR . $productImage->getFilenameMiddle();
        $this->filesystemWorker->remove($mediumFilePath);


        $bigFilePath = $productImagesDir . DIRECTORY_SEPARATOR . $productImage->getFilenameBig();
        $this->filesystemWorker->remove($bigFilePath);

        $product = $productImage->getProduct();
        $product->removeProductImage($productImage);

        $this->entityManager->flush();
    }

    public function getRepository(): ObjectRepository
    {
       return $this->entityManager->getRepository(ProductImage::class);
    }
}
