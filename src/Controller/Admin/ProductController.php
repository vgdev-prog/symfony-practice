<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\DTO\EditProductModel;
use App\Entity\Product;
use App\Form\Admin\CreateProductForm;
use App\Form\Handler\ProductFormHandler;
use App\Repository\ProductRepository;
use App\Utils\Manager\ProductManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/product', name: 'admin.product.')]
class ProductController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository $productRepository,
    )
    {
    }

    #[Route('/', name: 'listAll', methods: ['GET'])]
    public function listAll(ProductRepository $productRepository): Response
    {
       $products = $productRepository->findBy(
           criteria:['is_deleted' => false],
           orderBy: ['id' => 'DESC'],
           limit: 50);
        return $this->render('admin/product/index.html.twig', ['products' => $products]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    #[Route('/edit/{slug}', name: 'edit', methods: ['GET', 'POST'])]
    public function create(Request $request,ProductFormHandler $formHandler,  ?string $slug = null): Response
    {
        $productEntity = null;
        $isEdit = false;

        if ($slug) {
            $productEntity = $this->productRepository->findOneBySlug(slug: $slug);

            if (!$productEntity) {
                $this->addFlash('error', 'Product not found');
                return $this->redirectToRoute('admin.product.listAll');
            }
            $isEdit = true;
        } else {
            $productEntity = new Product();
        }

        $editModel = EditProductModel::makeFromProduct($productEntity);

        $form = $this->createForm(
            type: CreateProductForm::class,
            data: $editModel
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formHandler->processEditForm($editModel, $productEntity);

            $message = $isEdit ? 'Product updated' : 'Product created';
            $this->addFlash('success', $message);

            return $this->redirectToRoute('admin.product.listAll');
        }

        $images = $productEntity->getId()
            ? $productEntity->getProductImages()->getValues()
            : [];

        return $this->render('admin/product/create.html.twig',
            [
                'form' => $form,
                'product' => $productEntity,
                'isEdit' => $isEdit,
                'images' => $images
            ]
        );
    }

    #[Route('/{id}', name: 'delete', methods: ['GET'])]
    public function delete(Product $product, ProductManager $productManager, Request $request): Response
    {
        $token = $request->getSession()->get('_token');
        if ($this->isCsrfTokenValid('delete-product-'.$product->getId(), $token)) {
            return $this->redirectToRoute('admin.product.listAll');
        }
        $productManager->remove($product);

        $this->addFlash('success', 'Product deleted');

       return $this->redirectToRoute('admin.product.listAll');
    }
}
