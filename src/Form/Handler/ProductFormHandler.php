<?php

declare (strict_types=1);

namespace App\Form\Handler;

use App\Entity\Product;
use App\Utils\File\FileSaver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;

class ProductFormHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FileSaver $fileSaver,
    ) {
    }

    public function processEditForm(Product $product, Form $form): Product
    {
        $this->entityManager->persist($product);

        $newImageFile = $form->get('newImage')
                             ->getData();

        $tempImageFileName = $newImageFile
            ? $this->fileSaver->saveUploadedFileIntoTemp($newImageFile)
            : null;

        //TODO: Add new images with different sizes to the product
        //1. Save product's changes (+)
        //2. Save uploaded file into temp folder

        //3. Work with product (addProductImage) and ProductImage
        //3.1 Get folder with images of product

        //3.2 Work with ProductImage
        //3.2.1 Resize and save image into folder (BIG,MIDDLE,SMALL)
        //3.2.2 Create ProductImage and return it to Product

        //3.3 Save Product with new ProductImage
        $this->entityManager->flush();
        return $product;
    }

    public function uploadedFileIntoTemp()
    {

    }

}
