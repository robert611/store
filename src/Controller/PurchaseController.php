<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Purchase;
use App\Entity\Product;
use App\Entity\DeliveryType;
use App\Entity\PurchaseProduct;
use App\Entity\Basket;
use App\Form\UserAddressType;

class PurchaseController extends AbstractController
{
    /**
     * @Route("purchase/basket/summary", name="purchase_basket_summary")
     * @IsGranted("ROLE_USER")
     */
    public function basketPurchaseSummary(Request $request)
    {
        $form = $this->createForm(UserAddressType::class);

        $basket = $this->getDoctrine()->getRepository(Basket::class)->findBy(['user' => $this->getUser()]);

        $productsPrice = array_sum((new ArrayCollection($basket))->map(function($basketElement) {
            return $basketElement->getProduct()->getPrice() * $basketElement->getQuantity();
        })->toArray());

        return $this->render('purchase/basket_summary.html.twig',
            ['basket' => $basket, 'productsPrice' => $productsPrice, 'form' => $form->createView(), 
                'itemsQuantity' => $request->request->get('items-quantity')]);
    }

    /**
     * @Route("purchase/{id}/summary", name="purchase_summary")
     * @IsGranted("ROLE_USER")
     */
    public function purchaseSummary(Request $request, Product $product)
    {
        $form = $this->createForm(UserAddressType::class);

        $itemsQuantity = $request->request->get('items-quantity');

        if ($itemsQuantity > $product->getQuantity()) {
            $this->addFlash('warning', "Wystawione jest {$product->getQuantity()} sztuk tego przedmiotu, a ty próbujesz kupić {$itemsQuantity} sztuk.");

            return $this->redirectToRoute('product_show', ['id' => $product->getId()]);
        }

        return $this->render('purchase/summary.html.twig',
            ['product' => $product, 'form' => $form->createView(), 'itemsQuantity' => $itemsQuantity]);
    }

    /**
     * @Route("purchase/{id}/{deliveryTypeId}/{itemsQuantity}/buy", name="purchase_buy")
     * @IsGranted("ROLE_USER")
     */
    public function buy(Request $request, Product $product, $deliveryTypeId, $itemsQuantity)
    {
        $this->denyAccessUnlessGranted('PURCHASE_BUY', $product);

        $deliveryType = $this->getDoctrine()->getRepository(DeliveryType::class)->find($deliveryTypeId);

        $purchase = new Purchase();

        $purchase->setUser($this->getUser());
        $purchase->setCreatedAt(new \DateTime());
        $purchase->setPrice(($product->getPrice() * $itemsQuantity) + $deliveryType->getDefaultPrice());

        $purchaseProduct = new PurchaseProduct();

        $purchaseProduct->setPurchase($purchase);
        $purchaseProduct->setDeliveryType($deliveryType);
        $purchaseProduct->setProduct($product);
        $purchaseProduct->setQuantity($itemsQuantity);

        $entityManager = $this->getDoctrine()->getManager();

        if ($deliveryType->getPayment() == "cash-on-delivery") {
            $purchaseProduct->setIsPaid(2);
        } else {
            $purchaseProduct->setIsPaid(0);
        }

        $newProductQuantity = $product->getQuantity() - $itemsQuantity;

        if ($newProductQuantity < 1) {
            $product->setIsSoldOut(true);
        }

        $product->setQuantity($newProductQuantity);

        $entityManager->persist($product);
        $entityManager->persist($purchase);
        $entityManager->persist($purchaseProduct);
        $entityManager->flush();

        if ($deliveryType->getPayment() == "prepayment") {
            return $this->redirectToRoute('purchase_payment_view', ['id' => $purchase->getId()]);
        }

        return $this->redirectToRoute('purchase_after_buy_message');
    }

    /**
     * @Route("purchase/basket/buy", name="purchase_basket_buy")
     * @IsGranted("ROLE_USER")
     */
    public function buyWithBasket(Request $request)
    {
        $basketProducts = $this->getDoctrine()->getRepository(Basket::class)->findBy(['user' => $this->getUser()]);

        $productsPrice = 0;

        $products = (new ArrayCollection($basketProducts))->map(function($element) use (&$productsPrice) {
            $productsPrice += $element->getProduct()->getPrice() * $element->getQuantity();
            return $element->getProduct();
        });

        $this->denyAccessUnlessGranted('PURCHASE_BUY_WITH_BASKET', $products);

        $productsDeliveryTypes = array();

        $deliveriesPrice = 0;

        foreach($request->request->get('productDeliveryType') as $key => $delivery) {
            $productsDeliveryTypes[$key] = $this->getDoctrine()->getRepository(DeliveryType::class)->find($delivery);
            $deliveriesPrice += $productsDeliveryTypes[$key]->getDefaultPrice();
        }

        $entityManager = $this->getDoctrine()->getManager();

        $purchase = new Purchase();

        $purchase->setUser($this->getUser());
        $purchase->setCreatedAt(new \DateTime());
        $purchase->setPrice($productsPrice + $deliveriesPrice);

        $isProductWithPrepayment = false;

        foreach ($basketProducts as $basketProduct)
        {
            $purchaseProduct = new PurchaseProduct();

            $productDeliveryType = $productsDeliveryTypes[$basketProduct->getProduct()->getId()];

            $product = $basketProduct->getProduct();

            $purchaseProduct->setPurchase($purchase);
            $purchaseProduct->setDeliveryType($productDeliveryType);
            $purchaseProduct->setProduct($product);
            $purchaseProduct->setQuantity($basketProduct->getQuantity());

            if ($productDeliveryType->getPayment() == "cash-on-delivery") {
                $purchaseProduct->setIsPaid(2);
            } else {
                $isProductWithPrepayment = true;
                $purchaseProduct->setIsPaid(0);
            }
    
            $entityManager->persist($purchaseProduct);
            $entityManager->remove($basketProduct);

            $newProductQuantity = $product->getQuantity() - $basketProduct->getQuantity();

            if ($newProductQuantity < 1) {
                $product->setIsSoldOut(true);
            }

            $product->setQuantity($newProductQuantity);

            /* Persist changes in product quantity, and it being sold out */
            $entityManager->persist($product);
        }

        $entityManager->persist($purchase);
        $entityManager->flush();

        return new JsonResponse(['purchase_id' => $purchase->getId(), 'prepayment' => $isProductWithPrepayment]);
    }

    /**
     * @Route("purchase/after/buy/message", name="purchase_after_buy_message")
     * @IsGranted("ROLE_USER")
     */
    public function showMessageAfterBuying()
    {
        return $this->render('purchase/message_after_buying_product.html.twig', []);
    }
}
