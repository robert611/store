<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Product;
use App\Entity\AuctionBid;
use App\Model\AddProductToBasket;

/**
 * @IsGranted("ROLE_USER")
 */
class AuctionController extends AbstractController
{
    /**
     * @Route("/auction/bid/{id}", name="auction_bid")
     */
    public function bid(Product $product, Request $request, AddProductToBasket $addProductToBasketModel): Response
    {
        $this->denyAccessUnlessGranted('AUCTION_BID', $product);

        $bidPrice = $request->request->get('bid-price');

        if ($bidPrice == null) {
            return $this->redirectToRoute('index');
        }

        if ($bidPrice <= $product->getPrice()) {
            $this->addFlash('warning', 'Musisz podać wyższą propozycję niż ta obecna');
            return $this->redirectToRoute('product_show', ['id' => $product->getId()]);
        }

        $product->setPrice($bidPrice);

        $auctionBid = new AuctionBid();

        $auctionBid->setBid($bidPrice);
        $auctionBid->setUser($this->getUser());
        $auctionBid->setProduct($product);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($product);
        $entityManager->persist($auctionBid);
        $entityManager->flush();

        $addProductToBasketModel->addProductToBasket($product->getQuantity(), $product, $this->getUser());

        $this->addFlash('success', 'Przedmiot został zalicytowany');

        return $this->redirectToRoute('product_show', ['id' => $product->getId()]);
    }
}
