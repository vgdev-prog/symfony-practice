<?php

declare(strict_types=1);

namespace App\Controller\Admin;

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
    #[Route('/edit/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function create(Request $request,ProductFormHandler $formHandler,  ?int $id = null): Response
    {
        if ($id) {
            $product = $this->productRepository->find($id);
            if (!$product) {
                $this->addFlash('error', 'Product not found');
                return $this->redirectToRoute('admin.product.listAll');
            }
            $isEdit = true;
        } else {
            $product = new Product();
            $isEdit = false;
        }

        $form = $this->createForm(
            type: CreateProductForm::class,
            data: $product
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product = $formHandler->processEditForm($product, $form);
                $message = $isEdit ? 'Product updated' : 'Product created';
                $this->addFlash('success', $message);
                return $this->redirectToRoute('admin.product.listAll');
        }

        return $this->render('admin/product/create.html.twig',
            [
                'form' => $form,
                'product' => $product,
                'isEdit' => $isEdit,
                'images' => $product->getProductImages()->getValues()
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
