<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Product;
use App\Entity\DeliveryType;
use App\Form\ProductType;

/**
 * @IsGranted("ROLE_USER")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/account", name="account")
     */
    public function index()
    {
        return $this->render('account/index.html.twig', []);
    }

    /**
     * @Route("/account/product/new", name="account_product_new")
     */
    public function newProduct(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            
            $deliveryTypes = $request->request->get('product')["delivery_types"];

            $deliveryTypeRepository = $this->getDoctrine()->getRepository(DeliveryType::class);

            foreach ($deliveryTypes as $key => $type) 
            {
                $entityManager->persist($deliveryTypeRepository->find($type)->addProduct($product));
            }

            $product->addDeliveryType($this->getDoctrine()->getRepository(DeliveryType::class)->find(1));

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('account_new_product_message');
        }

        return $this->render('account/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/account/product/posting/message", name="account_new_product_message")
     */
    public function showMessageAfterPostingProduct()
    {
        return $this->render('account/product_posting_message.html.twig');
    }
}
