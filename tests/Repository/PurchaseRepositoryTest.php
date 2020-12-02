<?php 

namespace App\Test\Repository;

use App\Entity\Purchase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Model\PurchaseCodeGenerator;

class PurchaseRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testIfCodeIsNotAvailable()
    {
        $purchaseRepository = $this->entityManager->getRepository(Purchase::class);

        $code = $purchaseRepository->findAll()[0]->getCode();

        $isCodeAvailable = $purchaseRepository->isCodeAvailable($code);

        $this->assertFalse($isCodeAvailable);
    }

    public function testIfCodeIsAvailable()
    {
        $purchaseRepository = $this->entityManager->getRepository(Purchase::class);

        $code = (new PurchaseCodeGenerator($purchaseRepository))->generate();

        $isCodeAvailable = $purchaseRepository->isCodeAvailable($code);

        $this->assertTrue($isCodeAvailable);
    }
}