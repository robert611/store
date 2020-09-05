<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\DeliveryType;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Model\UploadProductPictures;
use App\Model\SaveProductDeliveryTypes;
use App\Model\SaveProductProperties;

/**
 * @Route("admin/product")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="admin_product_index", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('admin/product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_product_show", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function show(Product $product): Response
    {
        return $this->render('admin/product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Product $product, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        $picturesToRemove = $request->request->get('product')['pictures_to_remove'] ?? [] ;

        if (count($picturesToRemove) >= $product->getProductPictures()->count()) {
            $this->addFlash('product_form_picture_error', 'Musisz zostawić przynajmniej jedno zdjęcie');
        }
        else if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $entityManager = $this->getDoctrine()->getManager();

            /* Delivery Types */
            $deliveryTypes = $form->get('delivery_types')->getData();

            (new SaveProductDeliveryTypes($this->getDoctrine()->getRepository(DeliveryType::class), $entityManager, $product))->update($deliveryTypes);

            /* Pictures */
            $uploadProductPictures = new UploadProductPictures($entityManager, $this->getParameter('pictures_directory'), $product);

            $uploadProductPictures->removePicturesAndPersistChangesToDatabase($picturesToRemove);

            $newPictures = $form->get('pictures')->getData();

            $newPictures ? $uploadProductPictures->uploadPicturesAndPersistToDatabase($newPictures, $slugger) : null;

            /* Product Properties */
            $saveProductProperties = new SaveProductProperties($entityManager, $product);
            $saveProductProperties->edit($request->request->get('product'));

            $entityManager->flush();

            return $this->redirectToRoute('admin_product_edit', ['id' => $product->getId()]);
        }

        return $this->render('admin/product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_product_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_product_index');
    }
}
