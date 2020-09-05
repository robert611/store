<?php 

namespace App\Model;

use App\Entity\ProductBasicProperty;
use App\Entity\ProductSpecificProperty;
use App\Entity\ProductPhysicalProperty;

class SaveProductProperties
{
    private $entityManager;
    private $product;

    public function __construct(object $entityManager, object $product)
    {
        $this->entityManager = $entityManager;
        $this->product = $product;
    }

    public function save($productForm)
    {
        $this->saveProperties($productForm['basic_properties'], ProductBasicProperty::class);
        $this->saveProperties($productForm['specific_properties'], ProductSpecificProperty::class);
        $this->saveProperties($productForm['physical_properties'], ProductPhysicalProperty::class);
    }

    public function edit($productForm)
    {
        /* First remove all of the properties, and then recreate it with data from the form */
        $this->removeAllProperties();

        $this->save($productForm);
    }

    private function saveProperties($properties, $entityObject)
    {
        $propertiesLength = count($properties['name']);

        for ($key = 0; $key < $propertiesLength; $key++)
        {
            /* If user wants to delete that one, do not recreate it */
            if (isset($properties['remove'][$key])) continue;

            if(strlen($properties['name'][$key]) == 0) continue;

            $productProperty = new $entityObject();

            $productProperty->setProperty($properties['name'][$key]);
            $productProperty->setValue($properties['value'][$key]);
            $productProperty->setProduct($this->product);
            
            $this->entityManager->persist($productProperty);
        }
    }

    private function removeAllProperties()
    {
        $productId = $this->product->getId();

        $this->entityManager->getRepository(ProductBasicProperty::class)->removeAll($productId);
        $this->entityManager->getRepository(ProductSpecificProperty::class)->removeAll($productId);
        $this->entityManager->getRepository(ProductPhysicalProperty::class)->removeAll($productId);
    }
}