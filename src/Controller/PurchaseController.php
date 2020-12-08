<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Model\PurchaseCodeGenerator;
use App\Entity\Purchase;
use App\Entity\Product;
use App\Entity\DeliveryType;
use App\Entity\PurchaseProduct;
use App\Entity\Basket;
use App\Form\UserAddressType;
use App\Message\PurchaseMessage;

class PurchaseController extends AbstractController
{
    private $bus;

    public function __construct(MessageBusInterface $bus) 
    {
        $this->bus = $bus;
    }

    /**
     * @Route("purchase/basket/summary", name="purchase_basket_summary")
     * @IsGranted("ROLE_USER")
     */
    public function basketPurchaseSummary(Request $request, PurchaseCodeGenerator $purchaseCodeGenerator)
    {
        $form = $this->createForm(UserAddressType::class);

        $basket = $this->getDoctrine()->getRepository(Basket::class)->findBy(['user' => $this->getUser()]);

        $productsPrice = array_sum((new ArrayCollection($basket))->map(function($basketElement) {
            return $basketElement->getProduct()->getPrice() * $basketElement->getQuantity();
        })->toArray());

        /* It will be used to identify if given purchase was already processed and user does not try to buy the same products multiple times */
        $code = $purchaseCodeGenerator->generate();

        return $this->render('purchase/basket_summary.html.twig',
            ['basket' => $basket, 'productsPrice' => $productsPrice, 'form' => $form->createView(), 
                'itemsQuantity' => $request->request->get('items-quantity'), 'code' => $code]);
    }

    /**
     * @Route("purchase/{id}/summary", name="purchase_summary")
     * @IsGranted("ROLE_USER")
     */
    public function purchaseSummary(Request $request, Product $product, PurchaseCodeGenerator $purchaseCodeGenerator)
    {
        $form = $this->createForm(UserAddressType::class);

        $itemsQuantity = $request->request->get('items-quantity');

        if (is_null($itemsQuantity)) {
            $this->addFlash('warning', "Musisz podać liczbę sztuk tego produktu, którą chcesz kupić.");

            return $this->redirectToRoute('product_show', ['id' => $product->getId()]);
        }

        /* It will be used to identify if given purchase was already processed and user does not try to buy the same products multiple times */
        $code = $purchaseCodeGenerator->generate();

        if ($itemsQuantity > $product->getQuantity()) {
            $this->addFlash('warning', "Wystawione jest {$product->getQuantity()} sztuk tego przedmiotu, a ty próbujesz kupić {$itemsQuantity} sztuk.");

            return $this->redirectToRoute('product_show', ['id' => $product->getId()]);
        }

        return $this->render('purchase/summary.html.twig',
            ['product' => $product, 'form' => $form->createView(), 'itemsQuantity' => $itemsQuantity, 'code' => $code]);
    }

    /**
     * @Route("purchase/buy", name="purchase_buy")
     * @IsGranted("ROLE_USER")
     */
    public function buy(Request $request)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($request->request->get('product_id'));

        $this->denyAccessUnlessGranted('PURCHASE_BUY', $product);

        $code = $request->request->get('code');

        if (is_object($this->getDoctrine()->getRepository(Purchase::class)->findOneBy(['code' => $code]))) {
            $this->addFlash('warning', 'Próbowałeś/aś dwukrotnie wykonać to samo zamówienie. Pierwszy zakup został zatwierdzony. Sprawdź kupione przedmioty, żeby upewnić się że wszystko jest w porządku.');

            return $this->redirectToRoute('index');
        }

        $itemsQuantity = (int) $request->request->get('items_quantity');

        $deliveryType = $this->getDoctrine()->getRepository(DeliveryType::class)->find($request->request->get('delivery_type_id'));

        $purchase = new Purchase();

        $purchase->setUser($this->getUser());
        $purchase->setCreatedAt(new \DateTime());
        $purchase->setPrice(($product->getPrice() * $itemsQuantity) + $deliveryType->getDefaultPrice());
        $purchase->setCode($code);

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

        $this->bus->dispatch(new PurchaseMessage($purchaseProduct->getId()));

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

        $code = $request->request->get('code');

        if (is_object($this->getDoctrine()->getRepository(Purchase::class)->findOneBy(['code' => $code]))) {
            $this->addFlash('warning', 'Próbowałeś/aś dwukrotnie wykonać to samo zamówienie. Pierwszy zakup został zatwierdzony. Sprawdź kupione przedmioty, żeby upewnić się że wszystko jest w porządku.');

            return $this->redirectToRoute('index');
        }

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
        $purchase->setCode($code);

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
