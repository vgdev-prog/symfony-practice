<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
class EditProductModel
{


    public int|null $id;
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Type('string')]
    #[Assert\Length(min: 3, max: 255)]
    public string|null $title;

    #[Assert\NotBlank()]
    #[Assert\Positive()]
    #[Assert\Type(
        type: 'numeric',
    )]

    public null|int $price;

    #[Assert\Image(
        maxSize: '5M',
        mimeTypes: ['image/png', 'image/jpeg'],
    )]
    public UploadedFile|null $newImage = null;

    #[Assert\NotBlank()]
    #[Assert\Type('integer')]
    #[Assert\Positive()]
    public int|null $quantity;

    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    #[Assert\Length(min: 1)]
    public string|null $description;
    public bool|null $is_published;
    public bool|null $is_deleted;

    public static function makeFromProduct(Product $product): self
    {

        $model = new self();
        $model->id = $product->getId();
        $model->title = $product->getTitle();
        $model->price = (int) $product->getPrice();
        $model->quantity = $product->getQuantity();
        $model->description = $product->getDescription();
        $model->is_published = $product->isPublished();
        $model->is_deleted = $product->isDeleted();

        return $model;
    }

    public function applyToProduct(Product $product): Product
    {
        $product->setTitle($this->title);
        $product->setDescription($this->description);
        $product->setQuantity($this->quantity);
        $product->setPrice($this->price);
        $product->setIsPublished($this->is_published);
        $product->setIsDeleted($this->is_deleted);

        return $product;
    }
}
