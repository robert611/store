<?php 

namespace App\Model;

use App\Entity\ProductPicture;

class UploadProductPictures
{
    private $entityManager;

    private $path;

    private $product;

    public function __construct(?object $entityManager, $path, $product)
    {
        $this->entityManager = $entityManager;
        $this->path = $path;
        $this->product = $product;
    }

    public function uploadPicturesAndPersistToDatabase(array $pictures, $slugger)
    {
        foreach ($pictures as $picture) {
            $productPicture = new ProductPicture();

            $originalFilename = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
            
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$picture->guessExtension();

            try {
                $picture->move(
                    $this->path,
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            $productPicture->setName($newFilename);
            $productPicture->setProduct($this->product);

            $this->product->addProductPicture($productPicture);

            $this->entityManager->persist($productPicture);
        }
    }

    public function removePicturesAndPersistChangesToDatabase($picturesToRemove)
    {
        foreach ($picturesToRemove as $key => $pictureId)
        {
            $picture = $this->entityManager->getRepository(ProductPicture::class)->find($key);

            $path = $this->path . "/" . $picture->getName();

            if(file_exists($path) && is_file($path))
            {
                unlink($path);
            }

            $this->entityManager->remove($picture);
        }
    }

    public function removeAllProductPictures()
    {
        $folderPath = $this->path;

        $this->product->getProductPictures()->map(function($picture) use($folderPath) {
            $path = $folderPath . "/" . $picture->getName();

            if(file_exists($path) && is_file($path))
            {
                unlink($path);
            }
        });
    }
}
