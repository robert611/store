<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Product;
use App\Entity\Basket;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @Route("/account/basket", name="account_basket")
     */
    public function basket()
    {
        $basketElements = $this->getDoctrine()->getRepository(Basket::class)->findBy(['user' => $this->getUser()]);

        $productsPrize = 0;

        (new ArrayCollection($basketElements))->map(function($element) use (&$productsPrize) {
            return $productsPrize += $element->getProduct()->getPrice();
        });

        return $this->render('account/basket.html.twig', ['basketElements' => $basketElements, 'productsPrize' => $productsPrize]);
    }

    /**
     * @Route("/account/basket/add/product/{id}", name="account_basket_add_product", methods={"POST"})
     */
    public function addProductToBasket(Product $product)
    {
        $basketRepository = $this->getDoctrine()->getRepository(Basket::class);

        if ($basketRepository->findOneBy(['user' => $this->getUser(), 'product' => $product])) {
            $basketRepository->increaseProductQuantity($this->getUser()->getId(), $product->getId());
        } else {
            $basketRepository->addProductToBasket($this->getUser()->getId(), $product->getId());
        }

        $this->addFlash('success', 'Przedmiot został doddany do koszyka.');

        return $this->redirectToRoute('product_show', ['id' => $product->getId()]);
    }

    /**
     * @Route("/account/basket/delete/product/{id}", name="account_basket_delete_product", methods={"DELETE"})
     */
    public function deleteBasketProduct(Request $request, Basket $basket)
    {
        if ($this->isCsrfTokenValid('basket_delete'.$basket->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($basket);
            $entityManager->flush();
        }

        return $this->redirectToRoute('account_basket');
    }

    /**
     * @Route("/account/product/posting/message/{productId}", name="account_new_product_message")
     */
    public function showMessageAfterPostingProduct($productId)
    {
        return $this->render('account/product_posting_message.html.twig', ['productId' => $productId]);
    }
}
