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

    public function saveBasicProperties(array $basicProperties)
    {
        $propertiesLength = count($basicProperties['name']);

        for ($key = 0; $key < $propertiesLength; $key++)
        {
            $productBasicProperty = new ProductBasicProperty();

            $productBasicProperty->setProperty($basicProperties['name'][$key]);
            $productBasicProperty->setValue($basicProperties['value'][$key]);
            $productBasicProperty->setProduct($this->product);
            
            $this->entityManager->persist($productBasicProperty);
        }

    }

    public function saveSpecificProperties(array $specificProperties)
    {
        $propertiesLength = count($specificProperties['name']);

        for ($key = 0; $key < $propertiesLength; $key++)
        {
            $productSpecificProperty = new ProductSpecificProperty();

            $productSpecificProperty->setProperty($specificProperties['name'][$key]);
            $productSpecificProperty->setValue($specificProperties['value'][$key]);
            $productSpecificProperty->setProduct($this->product);
            
            $this->entityManager->persist($productSpecificProperty);
        }
    }

    public function savePhysicalProperties(array $physicalProperties)
    {
        $propertiesLength = count($physicalProperties['name']);

        for ($key = 0; $key < $propertiesLength; $key++)
        {
            $productPhysicalProperty = new ProductPhysicalProperty();

            $productPhysicalProperty->setProperty($physicalProperties['name'][$key]);
            $productPhysicalProperty->setValue($physicalProperties['value'][$key]);
            $productPhysicalProperty->setProduct($this->product);
            
            $this->entityManager->persist($productPhysicalProperty);
        }
    }
}