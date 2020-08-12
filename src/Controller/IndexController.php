<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

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
     * @Route("/listing", name="product_listing")
     */
    public function productListing(Request $request)
    {
        $productName = $request->query->get('product');

        return $this->render('index/product_listing.html.twig', ['productName' => $productName]);
    }
}
