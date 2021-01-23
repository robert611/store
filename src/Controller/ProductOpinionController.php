<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ProductOpinionType;
use App\Entity\ProductOpinion;
use App\Entity\Product;

class ProductOpinionController extends AbstractController
{
    /**
     * @Route("/product/opinion/{product}", name="new_product_opinion")
     */
    public function new(Product $product, Request $request): Response
    {
        $this->denyAccessUnlessGranted('CAN_ADD_PRODUCT_OPINION', $product);

        $opinion = new ProductOpinion();

        $form = $this->createForm(ProductOpinionType::class, $opinion);
        $form->handleRequest($request);

        $mark = (int) $request->request->get('mark');
        
        if ($form->isSubmitted() && $mark == 0) {
            $this->addFlash('warning', 'Musisz podać ocenę tego produktu');
        } else if ($form->isSubmitted() && ($mark < 0 || $mark > 5)) {
            $this->addFlash('warning', 'Ocena musi znajdować się w przedziale od 1 do 5 gwiazdek');
        }
        else if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
        
            $opinion->setMark($request->request->get('mark'));

            $opinion->setUser($this->getUser());
            $opinion->setProduct($product);
            $opinion->setCreatedAt(new \DateTime());

            $product->addProductOpinion($opinion);

            $this->addFlash('success', 'Opinia została dodana');

            $entityManager->persist($opinion);
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('account_user_bought_products');
        }
        
        return $this->render('product_opinion/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/product/opinion/edit/{opinion}", name="edit_product_opinion")
     */
    public function edit(ProductOpinion $opinion, Request $request): Response
    {
        $this->denyAccessUnlessGranted('CAN_EDIT_PRODUCT_OPINION', $opinion->getProduct());

        $form = $this->createForm(ProductOpinionType::class, $opinion);
        $form->handleRequest($request);

        $product = $opinion->getProduct();

        $mark = (int) $request->request->get('mark');

        if ($form->isSubmitted() && $mark == 0) {
            $this->addFlash('warning', 'Musisz podać ocenę tego produktu');
        } else if ($form->isSubmitted() && ($mark < 0 || $mark > 5)) {
            $this->addFlash('warning', 'Ocena musi znajdować się w przedziale od 1 do 5 gwiazdek');
        }
        else if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $opinion->setMark($request->request->get('mark'));

            $entityManager->persist($opinion);
            $entityManager->flush();

            $this->addFlash('success', 'Opinia została zmieniona');
        }

        return $this->render('product_opinion/edit.html.twig', [
            'product' => $product,
            'opinion' => $opinion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/product/opinion/delete/{opinion}", name="product_opinion_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ProductOpinion $opinion): Response
    {
        if ($this->isCsrfTokenValid('delete'.$opinion->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
                        
            $entityManager->remove($opinion);
            $entityManager->flush();

            $this->addFlash('success', 'Opinia została usunięta.');
        }

        return $this->redirectToRoute('account_user_bought_products');
    }
}
