<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Model\Paginator;
use App\Entity\Product;
use App\Entity\Purchase;
use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\UserAddress;
use App\Entity\PurchaseProduct;
use App\Form\MessageType;
use App\Form\UserAddressType;

/**
 * @IsGranted("ROLE_USER")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/account", name="account")
     */
    public function index(Request $request)
    {
        $userAddress = new UserAddress();

        $form = $this->createForm(UserAddressType::class, $userAddress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $userAddress->setUser($this->getUser());

            $entityManager->persist($userAddress);
            $entityManager->flush();

            return $this->redirectToRoute('account');
        }

        $announcements = count($this->getDoctrine()->getRepository(Product::class)->findBy(['owner' => $this->getUser(), 'auction_type' => 'free_advertisment']));
        $auctions = count($this->getDoctrine()->getRepository(Product::class)->findBy(['owner' => $this->getUser()])) - $announcements;

        $soldProducts = (new ArrayCollection($this->getDoctrine()->getRepository(PurchaseProduct::class)->findAll()))->filter(function($purchaseProduct) {
            return $purchaseProduct->getProduct()->getOwner()->getId() === $this->getUser()->getId();
        });

        $income = 0;
        
        $soldProducts->map(function($purchaseProduct) use (&$income) {
            $income += $purchaseProduct->getProduct()->getPrice() * $purchaseProduct->getQuantity();
        });

        return $this->render('account/index.html.twig',
            [
                'form' => $form->createView(),
                'auctions' => $auctions,
                'announcements' => $announcements, 
                'soldProducts' => count($soldProducts),
                'income' => $income
            ]
        );
    }

    /**
     * @Route("/account/user/auctions/list", name="account_user_auction_list")
     */
    public function userAuctionsList()
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findBy(['owner' => $this->getUser()]);

        return $this->render('account/user_auctions_list.html.twig', ['products' => $products]);
    }

    /**
     * @Route("/account/user/products/bought", name="account_user_bought_products")
     */
    public function boughtProducts(Request $request)
    {
        $purchases = $this->getDoctrine()->getRepository(Purchase::class)->findBy(['user' => $this->getUser()]);

        $boughtProducts = array();

        (new ArrayCollection($purchases))->map(function($purchase) use (&$boughtProducts) {
            foreach($purchase->getPurchaseProducts() as $product) {
                $boughtProducts[] = $product;
            }
        });

        $currentPage = $request->query->get('page') ? $request->query->get('page') : 1;
        $paginator = new Paginator(10, $boughtProducts, $currentPage);

        $boughtProducts = $paginator->getUnitsForThisPage();

        $pages = $paginator->getNumberOfPages();

        return $this->render('account/bought_products.html.twig', ['boughtProducts' => $boughtProducts, 'pages' => $pages, 'currentPage' => $currentPage]);
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
     * @Route("/account/user/conversation/{id}", name="account_show_user_conversation")
     */
    public function showConversation(Request $request, Conversation $conversation)
    {
        $this->denyAccessUnlessGranted('CONVERSATION_VIEW', $conversation);

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

            return $this->redirectToRoute('account_show_user_conversation', ['id' => $conversation->getId()]);
        }

        return $this->render('account/show_conversation.html.twig', [
            'conversation' => $conversation,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/account/product/posting/message/{id}", name="account_new_product_message")
     */
    public function showMessageAfterPostingProduct(Product $product)
    {
        $this->denyAccessUnlessGranted('PRODUCT_SHOW_POST_MESSAGE', $product);

        return $this->render('account/product_posting_message.html.twig', ['productId' => $product->getId()]);
    }

    /**
     * @Route("/account/change/email", name="account_change_email")
     */
    public function changeEmail(Request $request)
    {
        $newEmail = $request->request->get('new-email');
        $newEmailRepeat = $request->request->get('new-email-repeat');

        $password = $request->request->get('password');

        if ($newEmail !== $newEmailRepeat) {
            $this->addFlash('warning', 'Musisz podać dwa takie same adresy email.');

            return $this->redirectToRoute('account');
        }

        $verification = password_verify($password, $this->getUser()->getPassword());

        if (!$verification) {
            $this->addFlash('warning', 'Podano nieprawidłowe hasło.');

            return $this->redirectToRoute('account');
        }

        $entityManager = $this->getDoctrine()->getManager();

        $this->getUser()->setEmail($newEmail);

        $entityManager->persist($this->getUser());
        $entityManager->flush();

        $this->addFlash('success', 'Twój adres email został zmieniony.');

        return $this->redirectToRoute('account');
    }

    /**
     * @Route("/account/change/password", name="account_change_password")
     */
    public function changePassword(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $password = $request->request->get('new-password');
        $passwordRepeat = $request->request->get('new-password-repeat');

        $verification = password_verify($request->request->get('current-password'), $this->getUser()->getPassword());

        if (!$verification) {
            $this->addFlash('warning', 'Podano nieprawidłowe hasło.');

            return $this->redirectToRoute('account');
        }

        
        if ($password !== $passwordRepeat) {
            $this->addFlash('warning', 'Podane hasła nie są takie same.');

            return $this->redirectToRoute('account');
        }

        if (strlen($password) < 6 || strlen($password) > 32) {
            $this->addFlash('warning', 'Twoje hasło musi mieć przynajmniej 6 i nie więcej niż 32 znaki.');

            return $this->redirectToRoute('account');
        }

        /* Encode password */
        $this->getUser()->setPassword(
            $passwordEncoder->encodePassword(
                $this->getUser(),
                $password
            )
        );

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($this->getUser());
        $entityManager->flush();

        $this->addFlash('success', 'Twoje hasło zostało zmienione.');

        return $this->redirectToRoute('account');
    }
}
