<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Product;
use App\Entity\DeliveryType;
use App\Form\ProductType;
use App\Model\SaveProductProperties;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Model\UploadProductPictures;
use App\Model\SaveProductDeliveryTypes;

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

            $product->setOwner($this->getUser());
            
            $pictures = $form->get('pictures')->getData();

            /* Make sure there is at least 1 picture and less than 24, It can be done in ProductType with constraints because it would affect also editting */
            if (!$this->validatePictures($pictures))
            {
                return $this->render('account/index.html.twig', [
                    'form' => $form->createView()
                ]);
            }
            
            $uploadProductPictures = new UploadProductPictures($entityManager, $this->getParameter('pictures_directory'), $product);

            $uploadProductPictures->uploadPicturesAndPersistToDatabase($pictures, $slugger);

            $deliveryTypes = $form->get('delivery_types')->getData();

            (new SaveProductDeliveryTypes($this->getDoctrine()->getRepository(DeliveryType::class), $entityManager, $product))->save($deliveryTypes);

            $saveProductProperties = new SaveProductProperties($entityManager, $product);
            $saveProductProperties->save($request->request->get('product'));

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('account_new_product_message', ['productId' => $product->getId()]);
        }

        return $this->render('account/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/account/product/posting/message/{productId}", name="account_new_product_message")
     */
    public function showMessageAfterPostingProduct($productId)
    {
        return $this->render('account/product_posting_message.html.twig', ['productId' => $productId]);
    }

    public function validatePictures($pictures)
    {
        if (count($pictures) < 1 || count($pictures) > 24) 
        {
            $this->addFlash('product_form_picture_error', 'Wybierz przynajmniej jedno zdjęcie i nie więcej niż 24');

            return false;
        }

        return true;
    }
}
