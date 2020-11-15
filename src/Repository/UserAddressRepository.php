<?php

namespace App\Repository;

use App\Entity\UserAddress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserAddress|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserAddress|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserAddress[]    findAll()
 * @method UserAddress[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserAddressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAddress::class);
    }

    function remove($id)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            DELETE FROM user_address
            WHERE id = :id
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);

        return true;
    }

    function save($userAddress)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            INSERT INTO user_address
            VALUES (null, :user_id, :name, :surname, :address, :zip_code, :city, :country, :phone_number)
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['user_id' => $userAddress->getUser()->getId(), 'name' => $userAddress->getName(), 'surname' => $userAddress->getSurname(), 
            'address' => $userAddress->getAddress(), 'zip_code' =>  $userAddress->getZipCode(), 'city' => $userAddress->getCity(), 'country' => $userAddress->getCountry(), 
            'phone_number' => $userAddress->getPhoneNumber()]);

        return true;
    }
}
