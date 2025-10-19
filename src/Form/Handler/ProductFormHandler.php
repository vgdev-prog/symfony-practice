<?php

declare (strict_types=1);

namespace App\Form\Handler;

use App\Entity\Product;
use App\Utils\File\FileSaver;
use App\Utils\Manager\ProductManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;

class ProductFormHandler
{
    public function __construct(
        private ProductManager $productManager,
        private FileSaver $fileSaver,
    ) {
    }

    public function processEditForm(Product $product, Form $form): Product
    {
        //TODO: Add new images with different sizes to the product
        $this->productManager->save($product);

        $newImageFile = $form->get('newImage')
                             ->getData();

        $tempImageFileName = $newImageFile
            ? $this->fileSaver->saveUploadedFileIntoTemp($newImageFile)
            : null;


        $this->productManager->updateProductImages($product, $tempImageFileName);

        $this->productManager->save($product);

        return $product;
    }

}
