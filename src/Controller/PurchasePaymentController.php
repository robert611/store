<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\PurchaseProduct;
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
     * @Route("purchase/{id}/payment/{productId}", name="purchase_payment")
     * @IsGranted("ROLE_USER")
     */
    public function purchasePayment(Purchase $purchase, $productId)
    {
        $purchaseProduct = $purchase->getPurchaseProducts()->filter(function ($item) use ($productId) {
			return $item->getProduct()->getId() == $productId;
        })->first();
        
        if ($purchaseProduct->getDeliveryType()->getPayment() == "cash-on-delivery") {
            return new JsonResponse(['error' => "Nie możesz zapłacić za przedmiot z dostawą za pobraniem. Jeśli wyświetla ci się informacja o wcześniejszej płatności, proszę ją pominąć."]);
        }

		$product = $purchaseProduct->getProduct();

        \Stripe\Stripe::setApiKey($this->getParameter('stripe_secret_key'));

        $checkoutSession = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
				[
              		'price_data' => [
						'currency' => 'pln',
						'unit_amount' => $product->getPrice() * 100,
						'product_data' => [
						    'name' => $product->getName(),
						    'images' => ['http://localhost:8000/' . $product->getProductPictures()[0]->getName()],
						],
              		],
            		'quantity' => $purchaseProduct->getQuantity(),
                ],
                [
                    'price_data' => [
                        'currency' => 'pln',
                        'unit_amount' => $purchaseProduct->getDeliveryType()->getDefaultPrice() * 100,
                        'product_data' => [
                            'name' => 'Przesyłka'
                        ]
                    ],
                    'quantity' => 1
                ]
            ],
            'mode' => 'payment',
            'success_url' => 'http://localhost:8000' . '/set/purchase/product/payment/status/' . $purchaseProduct->getId(),
            'cancel_url' => 'http://localhost:8000' . '/account/user/products/bought',
        ]);

        return new JsonResponse(['id' => $checkoutSession->id]);
    }

    /**
     * @Route("set/purchase/product/payment/status/{id}", name="set_purchase_product_payment_status")
     * @IsGranted("ROLE_USER")
     */
    public function setPurchasePaymentStatus(PurchaseProduct $purchaseProduct)
    {
        $purchaseProduct->setIsPaid(true);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($purchaseProduct);
        $entityManager->flush();

        return $this->redirectToRoute('purchase_after_buy_message');
    }
}
