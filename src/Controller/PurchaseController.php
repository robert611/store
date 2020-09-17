<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Purchase;
use App\Entity\Product;
use App\Entity\DeliveryType;
use App\Entity\PurchaseProduct;
use App\Form\UserAddressType;

class PurchaseController extends AbstractController
{

    /**
     * @Route("purchase/{id}/summary", name="purchase_summary")
     * @IsGranted("ROLE_USER")
     */
    public function purchaseSummary(Product $product)
    {
        $form = $this->createForm(UserAddressType::class);

        return $this->render('purchase/summary.html.twig',
            ['product' => $product, 'form' => $form->createView()]);
    }

    /**
     * @Route("purchase/{id}/payment", name="purchase_payment")
     * @IsGranted("ROLE_USER")
     */
    public function purchasePayment(Product $product)
    {
        return $this->render('purchase/payment.html.twig',
            ['product' => $product]);
    }

    /**
     * @Route("purchase/{id}/{delivery_type}/buy", name="purchase_buy")
     * @IsGranted("ROLE_USER")
     */
    public function buy(Request $request, Product $product, $deliveryType)
    {
        $this->denyAccessUnlessGranted('PURCHASE_BUY', $product);

        $purchase = new Purchase();

        $purchase->setOwner($this->getUser());
        $purchase->setCreatedAt(new \DateTime());

        $purchaseProduct = new PurchaseProduct();

        $purchaseProduct->setPurchase($purchase);
        $purchaseProduct->setPaymentMethod('default');
        $purchaseProduct->setProduct($product);
        $purchaseProduct->setQuantity(1);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($purchase);
        $entityManager->persist($purchaseProduct);
        $entityManager->flush();

        return $this->redirectToRoute('purchase_after_buy_message');
    }

    /**
     * @Route("purchase/basket/buy", name="purchase_basket_buy")
     * @IsGranted("ROLE_USER")
     */
    public function buyWithBasket(Request $request)
    {
        $this->denyAccessUnlessGranted('PURCHASE_BUY_WITH_BASKET', $season);

        $purchase = new Purchase();

        $purchase->setOwner($this->getUser());
        $purchase->setCreatedAt(new \DateTime());

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($purchase);
        $entityManager->flush();

        return $this->redirectToRoute('purchase_after_buy_message');
    }

    /**
     * @Route("purchase/after/buy/message", name="purchase_after_buy_message")
     * @IsGranted("ROLE_USER")
     */
    private function showMessageAfterBuying()
    {
        return $this->render('purchase/message_after_buying_product.html.twig', []);
    }
}
