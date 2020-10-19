<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Basket;
use App\Entity\Product;
use App\Model\AddProductToBasket;

class BasketController extends AbstractController
{
    /**
     * @Route("basket", name="basket")
     */
    public function basket()
    {
        $basketElements = $this->getDoctrine()->getRepository(Basket::class)->findBy(['user' => $this->getUser()]);

        $productsPrize = 0;

        (new ArrayCollection($basketElements))->map(function($element) use (&$productsPrize) {
            return $productsPrize += $element->getProduct()->getPrice() * $element->getQuantity();
        });

        return $this->render('basket/basket.html.twig', ['basketElements' => $basketElements, 'productsPrize' => $productsPrize]);
    }

    /**
     * @Route("/account/basket/add/product/{id}", name="basket_add_product", methods={"POST"})
     */
    public function addProductToBasket(Request $request, Product $product, AddProductToBasket $addProductToBasketModel)
    {
        if ($this->isCsrfTokenValid('basket_add_product'.$product->getId(), $request->request->get('_token'))) {
            $quantity = $request->request->get('items-quantity');

            $addProductToBasketModel->addProductToBasket($quantity, $product, $this->getUser());

            if ($addProductToBasketModel->isQuantityToBig()) {
                $this->addFlash('warning', "Nie możesz mieć w koszyku większej ilości tego produktu niż jest go w sprzedaży. Zamiast {$quantity} sztuk zostanie dodane {$addProductToBasketModel->getQuantityPossibleToAdd()} sztuk.");
            } else {
                $this->addFlash('success', 'Przedmiot został doddany do koszyka.');
            }
        }

        return $this->redirectToRoute('product_show', ['id' => $product->getId()]);
    }

    /**
     * @Route("/account/basket/delete/product/{id}", name="basket_delete_product", methods={"DELETE"})
     */
    public function deleteBasketProduct(Request $request, Basket $basket)
    {
        if ($this->isCsrfTokenValid('basket_delete'.$basket->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($basket);
            $entityManager->flush();
        }

        return $this->redirectToRoute('basket');
    }
}
