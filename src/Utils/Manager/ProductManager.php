<?php

declare(strict_types=1);

namespace App\Utils\Manager;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;


/**
 * @template ModelEntity of Product
 */
class ProductManager extends AbstractBaseManager
{
    public function __construct(
        EntityManagerInterface $entityManager,
        private ProductImageManager $productImageManager,
        private string $productImagesDir,
    )
    {
        parent::__construct($entityManager);
    }

    public function getRepository(): ObjectRepository
    {
        return $this->entityManager->getRepository(Product::class);
    }

    /**
     * @param ModelEntity $entity
     * @return void
     */
    public function remove(object $entity): void
    {
        $entity->setIsDeleted(true);
        $this->save($entity);
    }

    public function getProductImagesDir(Product $product): string
    {
        return sprintf('%s/%s', $this->productImagesDir, $product->getId());
    }

    public function updateProductImages(Product $product, string $tempImageFilename): Product
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
