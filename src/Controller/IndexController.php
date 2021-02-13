<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\DeliveryType;
use App\Model\Paginator;
use App\Model\CalculateFilterPrices;

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
    public function productListing(Request $request, CalculateFilterPrices $calculateFilterPrices)
    {
        $productName = $request->query->get('product');
        $productCategory = $request->query->get('category');
        $owner = $request->query->get('owner');

        $currentPage = $request->query->get('page') ? $request->query->get('page') : 1;

        $products = $this->getDoctrine()->getRepository(Product::class)->findProductByNameAndCategory($productName, $productCategory, $owner);

        $filterPrices = $calculateFilterPrices->getFitlerPrices($products);

        $paginator = new Paginator(12, $products, $currentPage);

        $thisPageProducts = $paginator->getUnitsForThisPage();

        $pages = $paginator->getNumberOfPages();

        $deliveryTypes = $this->getDoctrine()->getRepository(DeliveryType::class)->findAll();

        return $this->render('index/product_listing.html.twig', [
            'productName' => $productName, 
            'products' => $thisPageProducts, 
            'pages' => $pages,
            'currentPage' => $currentPage,
            'deliveryTypes' => $deliveryTypes,
            'productCategory' => $productCategory,
            'productOwner' => $owner,
            'filterPrices' => $filterPrices
        ]);
    }
}
