<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Purchase;

class PurchasePaymentController extends AbstractController
{
    /**
     * @Route("purchase/{id}/payment/view", name="purchase_payment_view")
     * @IsGranted("ROLE_USER")
     */
    public function purchasePaymentView(Purchase $purchase)
    {
      	return $this->render('purchase/payment.html.twig',
            ['purchase' => $purchase]);
    }

    /**
     * @Route("purchase/{id}/payment", name="purchase_payment")
     * @IsGranted("ROLE_USER")
     */
    public function purchasePayment(Purchase $purchase)
    {
        $product = $purchase->getPurchaseProducts()->first()->getProduct();

        \Stripe\Stripe::setApiKey($this->getParameter('stripe_secret_key'));

        $checkoutSession = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
              'price_data' => [
                'currency' => 'pln',
                'unit_amount' => ($purchase->getPrice() * 100) + ($purchase->getPurchaseProducts()->first()->getDeliveryType()->getDefaultPrice() * 100),
                'product_data' => [
                  'name' => $product->getName(),
                  'images' => ['http://localhost:8000/' . $product->getProductPictures()[0]->getName()],
                ],
              ],
              'quantity' => $purchase->getPurchaseProducts()->first()->getQuantity(),
            ]],
            'mode' => 'payment',
            'success_url' => 'http://localhost:8000' . '/set/purchase/payment/status/' . $purchase->getId(),
            'cancel_url' => 'http://localhost:8000' . '/purchase/payment/fail/message',
        ]);

        return new JsonResponse(['id' => $checkoutSession->id]);
    }

    /**
     * @Route("set/purchase/payment/status/{id}", name="set_purchase_payment_status")
     * @IsGranted("ROLE_USER")
     */
    public function setPurchasePaymentStatus(Purchase $purchase)
    {
        $purchase->setIsPaid(true);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($purchase);
        $entityManager->flush();

        return $this->redirectToRoute('purchase_after_buy_message');
    }
}
