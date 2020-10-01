<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Product;
use App\Entity\Purchase;
use App\Entity\Conversation;
use App\Entity\Message;
use App\Form\MessageType;

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
     * @Route("/account/user/acutions/list", name="account_user_auction_list")
     */
    public function userAuctionsList()
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findBy(['owner' => $this->getUser()]);

        return $this->render('account/user_auctions_list.html.twig', ['products' => $products]);
    }

    /**
     * @Route("/account/user/products/bought", name="account_user_bought_products")
     */
    public function boughtProducts()
    {
        $purchases = $this->getDoctrine()->getRepository(Purchase::class)->findBy(['user' => $this->getUser()]);

        return $this->render('account/bought_products.html.twig', ['purchases' => $purchases]);
    }

    /**
     * @Route("/account/user/conversations", name="account_user_conversations")
     */
    public function userConversations()
    {
        $conversations = array_merge(
            $this->getDoctrine()->getRepository(Conversation::class)->findBy(['author' => $this->getUser()]),
            $this->getDoctrine()->getRepository(Conversation::class)->findBy(['recipient' => $this->getUser()])
        );

        return $this->render('account/user_conversations.html.twig', ['conversations' => $conversations]);
    }

    /**
     * @Route("/account/user/conversation/{id}", name="account_user_conversation")
     */
    public function showConversation(Request $request, Conversation $conversation)
    {
        $message = new Message();

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $message->setAuthor($this->getUser());
            $message->setCreatedAt(new \DateTime());
            $message->setConversation($conversation);

            $conversation->addMessage($message);

            $entityManager->persist($conversation);
            $entityManager->persist($message);

            $entityManager->flush();

            return $this->redirectToRoute('account_user_conversation', ['id' => $conversation->getId()]);
        }

        return $this->render('account/show_conversation.html.twig', [
            'conversation' => $conversation,
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
}
