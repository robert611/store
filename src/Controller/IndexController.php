<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Product;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('index/index.html.twig');
    }

    /**
     * @Route("/product/{id}", name="product_show", methods={"GET"})
     */
    public function showProduct(Product $product): Response
    {
        return $this->render('index/show_product.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/listing", name="product_listing")
     */
    public function productListing(Request $request)
    {
        $productName = $request->query->get('product');

        return $this->render('index/product_listing.html.twig', ['productName' => $productName]);
    }
}
