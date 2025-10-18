<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use App\Form\EditProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        $productList = $this->entityManager->getRepository(Product::class)
                                           ->findAll();
        return $this->render('main/default/index.html.twig', [
            'products' => $productList,
        ]);
    }

    #[Route('/product/edit/{id}', name: 'product_edit', requirements: ['id' => '\d+'], methods: ["POST", "GET"])]
    #[Route('/product/add', name: 'product_add', methods: ["POST", "GET"])]
    public function editProduct(Request $request, int $id = null): Response
    {
        $product = $id
            ? $this->entityManager->getRepository(Product::class)
                                  ->find($id)
            : new Product();
        $form = $this->createForm(EditProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->entityManager->persist($product);
                $this->entityManager->flush();

                $this->addFlash(
                    'success',
                    $id
                        ? 'Product updated successfully.'
                        : 'Product created successfully.'
                );
                return $this->redirectToRoute('homepage');
            }
            $this->addFlash('error', 'Form contains errors');
        }

        $responce = $this->render('default/edit-product.html.twig', [
            'form' => $form
        ]);

        return $form->isSubmitted() && !$form->isValid()
            ? $responce->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)
            : $responce;
    }
}
