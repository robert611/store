<?php 

namespace App\Model;

use App\Repository\PurchaseRepository;

class PurchaseCodeGenerator 
{
    private $purchaseRepository;

    public function __construct(PurchaseRepository $purchaseRepository)
    {
        $this->purchaseRepository = $purchaseRepository;
    }

    public function generate(): string
    {
        $alphabet = range('A', 'Z');

        do {
            $code = null;

            for ($i = 1; $i <= 12; $i++) {
                $letterOrNumber = ceil(rand(0, 1));
                $code .= $letterOrNumber == 1 ? $alphabet[rand(0, count($alphabet) - 1)] : ceil(rand(0, 8));
            } 
        } while(!$this->purchaseRepository->isCodeAvailable($code));

        return $code;
    }
}