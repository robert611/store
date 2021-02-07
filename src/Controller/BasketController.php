<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\BasketRepository;
use App\Entity\Basket;
use App\Entity\Product;
use App\Model\AddProductToBasket;

/**
 * @IsGranted("ROLE_USER")
 */
class BasketController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("basket", name="basket")
     */
    public function basket(BasketRepository $basketRepository)
    {
        $basketElements = $basketRepository->findBy(['user' => $this->getUser()]);

        /* Check if products wchich are in user basket, were alraady bought by somebody */
        (new ArrayCollection($basketElements))->map(function($basketElement) {
            $product = $basketElement->getProduct();

            if ($basketElement->getQuantity() > $product->getQuantity()) {

                if ($product->getQuantity() > 0) {
                    $message = "Kilka sztuk produktu o tytule {$product->getName()} zostało sprzedanych, w wyniku czego liczba jego sztuk w twoim koszyku uległa zmianie.";

                    $basketElement->setQuantity($product->getQuantity());

                    $this->entityManager->persist($basketElement);
                } else {
                    $message = "Produkt o tytule {$product->getName()} został wyprzedany, w wyniku czego został usunięty z twojego koszyka.";

                    $this->entityManager->remove($basketElement);
                }

                $this->addFlash('warning', $message);
            }
        });

        $this->entityManager->flush();

        $basketElements = $basketRepository->findBy(['user' => $this->getUser()]);

        $productsPrize = array_sum((new ArrayCollection($basketElements))->map(function($basketElement) {
            $product = $basketElement->getProduct();

            if ($product->getAuctionType() == "buy_now" or !$product->isAuctionActive())
                return $product->getPrice() * $basketElement->getQuantity();
        })->toArray());

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
                $this->addFlash('success', 'Przedmiot został dodany do koszyka.');
            }
        }

        return $this->redirectToRoute('product_show', ['id' => $product->getId()]);
    }

    /**
     * @Route("/account/basket/delete/product/{id}", name="basket_delete_product", methods={"DELETE"})
     */
    public function deleteBasketProduct(Request $request, Basket $basket)
    {
        if($basket->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('basket');
        }
        
        if ($this->isCsrfTokenValid('basket_delete'.$basket->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($basket);
            $entityManager->flush();
        }

        return $this->redirectToRoute('basket');
    }
}
