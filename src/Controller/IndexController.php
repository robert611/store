<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Product;
use App\Entity\Category;
use App\Service\Paginator;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('index/index.html.twig', ['categories' => $categories]);
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
        $productCategory = $request->query->get('category');

        $page = $request->query->get('page') ? $request->query->get('page') : 1;

        $products = $this->getDoctrine()->getRepository(Product::class)->findProductByNameAndCategory($productName, $productCategory);

        $paginator = new Paginator(15, $products, $page);

        $products = $paginator->paginator();

        $pages = $paginator->getPages();

        return $this->render('index/product_listing.html.twig', [
            'productName' => $productName, 
            'products' => $products, 
            'pages' => $pages,
            'page' => $page
        ]);
    }
}
