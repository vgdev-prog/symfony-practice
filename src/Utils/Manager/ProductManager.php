<?php

declare(strict_types=1);

namespace App\Utils\Manager;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class ProductManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductImageManager $productImageManager,
        private string $productImagesDir,
    )
    {
    }


    /**
     * @return ObjectRepository
     */
    public function getRepository():ObjectRepository
    {
        $this->entityManager->getRepository(Product::class);
    }

    public function remove(Product $product)
    {
        $product->setIsDeleted(true);
        $this->save($product);
    }

    public function getProductImagesDir(Product $product)
    {
        return sprintf('%s/%s', $this->productImagesDir, $product->getId());
    }

    public function save(Product $product):void
    {
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    public function updateProductImages(Product $product, string $tempImageFilename)
    {
        if (!$tempImageFilename) {
            return $product;
        }

        $productDir = $this->getProductImagesDir($product);

        $productImage = $this->productImageManager->saveImageForProduct($productDir, $tempImageFilename);
        $productImage->setProduct($product);

        $product->addProductImage($productImage);

        return $product;
    }

}
