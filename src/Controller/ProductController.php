<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\DeliveryType;
use App\Entity\Conversation;
use App\Entity\Message;
use App\Form\ProductType;
use App\Form\MessageType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Model\UploadProductPictures;
use App\Model\SaveProductDeliveryTypes;
use App\Model\SaveProductProperties;
use App\Entity\Basket;

class ProductController extends AbstractController
{
    /**
     * @Route("admin/product", name="admin_product_index", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    /**
     * @Route("admin/product/{id}", name="admin_product_show", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function showAdmin(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("product/new", name="product_new")
     */
    public function new(Request $request, SluggerInterface $slugger)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $product->setIsSoldOut(false);
            $product->setIsDeleted(false);
            $product->setOwner($this->getUser());
            $product->setCreatedAt(new \DateTime());
            
            $pictures = $form->get('pictures')->getData();

            /* Make sure there is at least 1 picture and less than 24, It can be done in ProductType with constraints because it would affect also editting */
            if (!$this->validatePictures($pictures) or !$this->isAuctionDurationGiven($form->get('auction_type')->getData(), $form->get('duration')->getData()))
            {
                return $this->render('product/new.html.twig', [
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

            return $this->redirectToRoute('account_new_product_message', ['id' => $product->getId()]);
        }

        return $this->render('product/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("product/{id}", name="product_show", methods={"GET", "POST"})
     */
    public function show(Request $request, Product $product): Response
    {   
        $this->denyAccessUnlessGranted('CAN_SEE_PRODUCT', $product);

        $message = new Message();

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$this->getUser()) {
            $this->addFlash('warning', 'Musisz być zalogowany żeby wysłać wiadomość.');
        }
        if($form->isSubmitted() && $this->getUser() == $product->getOwner()) {
            $this->addFlash('warning', 'Nie możesz wysłać wiadomości sam do siebie.');
        }
        else if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            
            $conversation = $this->getDoctrine()->getRepository(Conversation::class)->findOneBy(['product' => $product]);

            if (!$conversation) {
                $conversation = new Conversation();

                $conversation->setAuthor($this->getUser());
                $conversation->setRecipient($product->getOwner());
                $conversation->setProduct($product);
            }

            $message->setAuthor($this->getUser());
            $message->setCreatedAt(new \DateTime());
            $message->setConversation($conversation);

            $conversation->addMessage($message);

            $entityManager->persist($conversation);
            $entityManager->persist($message);

            $entityManager->flush();

            return $this->redirectToRoute('product_show', ['id' => $product->getId()]);
        }

        $productInTheBasket = $this->getDoctrine()->getRepository(Basket::class)->findOneBy(['product' => $product, 'user' => $this->getUser()]);

        $productQuantityToBuyForUser = $product->getQuantity() - ($productInTheBasket ? $productInTheBasket->getQuantity() : 0);

        return $this->render('index/show_product.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
            'productQuantityToBuyForUser' => $productQuantityToBuyForUser
        ]);
    }

    /**
     * @Route("product/{id}/edit", name="product_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Product $product, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        $picturesToRemove = $request->request->get('product')['pictures_to_remove'] ?? [] ;

        $newPictures = $form->get('pictures')->getData();

        if (count($picturesToRemove) !== 0 && count($picturesToRemove) >= ($product->getProductPictures()->count() + count($newPictures))) {
            $this->addFlash('product_form_picture_error', 'Musisz zostawić przynajmniej jedno zdjęcie');
        }
        else if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $entityManager = $this->getDoctrine()->getManager();

            /* Delivery Types */
            $deliveryTypes = $form->get('delivery_types')->getData();

            (new SaveProductDeliveryTypes($this->getDoctrine()->getRepository(DeliveryType::class), $entityManager, $product))->update($deliveryTypes);

            /* Pictures */
            $uploadProductPictures = new UploadProductPictures($entityManager, $this->getParameter('pictures_directory'), $product);

            $uploadProductPictures->removePicturesAndPersistChangesToDatabase($picturesToRemove);

            $newPictures ? $uploadProductPictures->uploadPicturesAndPersistToDatabase($newPictures, $slugger) : null;

            /* Product Properties */
            $saveProductProperties = new SaveProductProperties($entityManager, $product);
            $saveProductProperties->edit($request->request->get('product'));

            $entityManager->flush();

            return $this->redirectToRoute('product_edit', ['id' => $product->getId()]);
        }

        if ($product->getProductPictures()->count() == 0) {
            $this->addFlash('product_form_picture_error', 'Każdy produkt powinien mieć przynajmniej jedno zdjęcie');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("product/{id}", name="product_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            
            $product->setIsDeleted(true);
            
            $entityManager->persist($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('account_user_auction_list');
    }

    public function validatePictures($pictures): bool
    {
        if (count($pictures) < 1 || count($pictures) > 24) 
        {
            $this->addFlash('product_form_picture_error', 'Wybierz przynajmniej jedno zdjęcie i nie więcej niż 24');

            return false;
        }

        return true;
    }

    public function isAuctionDurationGiven($auctionType, $duration): bool
    {
        if ($auctionType == "auction" && $duration == null) 
        {
            $this->addFlash('product_form_duration_errors', 'Musisz podać czas trwania licytacji.');

            return false;
        }

        return true;
    }
}
