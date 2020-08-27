<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Product;
use App\Entity\ProductPicture;
use App\Entity\DeliveryType;
use App\Form\ProductType;
use Symfony\Component\String\Slugger\SluggerInterface;

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
    public function newProduct(Request $request, SluggerInterface $slugger)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            
            $deliveryTypes = $request->request->get('product')["delivery_types"];

            $deliveryTypeRepository = $this->getDoctrine()->getRepository(DeliveryType::class);

            foreach ($deliveryTypes as $type) 
            {
                $entityManager->persist($deliveryTypeRepository->find($type)->addProduct($product));
            }

            $pictures = $form->get('pictures')->getData();
     
            /* Make sure there will not be more than 24 files */
            if (count($pictures) > 24) {
                $pictures = array_slice($pictures, 0, 24);
            }

            if ($pictures) {
                foreach ($pictures as $picture) 
                {
                    $productPicture = new ProductPicture();

                    $originalFilename = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
                    
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$picture->guessExtension();
    
                    try {
                        $picture->move(
                            $this->getParameter('pictures_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }

                    $productPicture->setName($newFilename);
                    $productPicture->setProduct($product);

                    $product->addProductPicture($productPicture);

                    $entityManager->persist($productPicture);
                }
            }

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
