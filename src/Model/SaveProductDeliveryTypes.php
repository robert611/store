<?php 

namespace App\Model;

class SaveProductDeliveryTypes
{
    private $deliveryTypeRepository;

    private $entityManager;

    private $product;

    public function __construct($deliveryTypeRepository, $entityManager, $product)
    {
        $this->deliveryTypeRepository = $deliveryTypeRepository;
        $this->entityManager = $entityManager;
        $this->product = $product;
    }

    public function save(object $deliveryTypes)
    {
        foreach ($deliveryTypes as $type) 
        {
            $this->entityManager->persist($this->deliveryTypeRepository->find($type)->addProduct($this->product));
        }
    }

    public function update(object $deliveryTypes)
    {
        /* First delete current product delivery types, then add new set of delivery types */
        $this->deliveryTypeRepository->removeAll($this->product->getId());

        $this->save($deliveryTypes);
    }
}