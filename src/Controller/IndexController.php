<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\DeliveryType;
use App\Service\Paginator;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

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
     * @Route("/listing", name="product_listing")
     */
    public function productListing(Request $request)
    {
        $productName = $request->query->get('product');
        $productCategory = $request->query->get('category');

        $currentPage = $request->query->get('page') ? $request->query->get('page') : 1;

        $products = $this->getDoctrine()->getRepository(Product::class)->findProductByNameAndCategory($productName, $productCategory);

        $paginator = new Paginator(15, $products, $currentPage);

        $products = $paginator->getUnitsForThisPage();

        $pages = $paginator->getNumberOfPages();

        $deliveryTypes = $this->getDoctrine()->getRepository(DeliveryType::class)->findAll();

        return $this->render('index/product_listing.html.twig', [
            'productName' => $productName, 
            'products' => $products, 
            'pages' => $pages,
            'currentPage' => $currentPage,
            'deliveryTypes' => $deliveryTypes
        ]);
    }
}
