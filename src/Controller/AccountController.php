<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Product;
use App\Entity\Purchase;

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
     * @Route("/account/product/posting/message/{productId}", name="account_new_product_message")
     */
    public function showMessageAfterPostingProduct($productId)
    {
        return $this->render('account/product_posting_message.html.twig', ['productId' => $productId]);
    }
}
