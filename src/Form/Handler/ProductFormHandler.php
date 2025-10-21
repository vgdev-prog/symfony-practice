<?php

declare (strict_types=1);

namespace App\Form\Handler;

use App\DTO\EditProductModel;
use App\Entity\Product;
use App\Utils\File\FileSaver;
use App\Utils\Manager\ProductManager;

class ProductFormHandler
{
    public function __construct(
        private ProductManager $productManager,
        private FileSaver $fileSaver,
    ) {
    }

    public function processEditForm(EditProductModel $productModel, Product $product): Product
    {
        $productModel->applyToProduct($product);
        $this->productManager->save($product);

        $tempImageFileName = $productModel->newImage
            ? $this->fileSaver->saveUploadedFileIntoTemp($productModel->newImage)
            : null;

        $this->productManager->updateProductImages($product, $tempImageFileName);

        $this->productManager->save($product);

        return $product;
    }

}
