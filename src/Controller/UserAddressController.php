<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use App\Entity\UserAddress;

/**
 * @IsGranted("ROLE_USER")
 */
class UserAddressController extends AbstractController
{
    /**
     * @Route("/user/address/new", name="user_address_new")
     */
    public function new()
    {
        $request = Request::createFromGlobals();

        $userAddress = new UserAddress();

        $entityManager = $this->getDoctrine()->getManager();

        $userAddress->setName($request->request->get('user_address')['name']);
        $userAddress->setSurname($request->request->get('user_address')['surname']);
        $userAddress->setAddress($request->request->get('user_address')['address']);
        $userAddress->setZipCode($request->request->get('user_address')['zip_code']);
        $userAddress->setCity($request->request->get('user_address')['city']);
        $userAddress->setCountry($request->request->get('user_address')['country']);
        $userAddress->setPhoneNumber($request->request->get('user_address')['phone_number']);
        $userAddress->setUser($this->getUser());

        $entityManager->persist($userAddress);
        $entityManager->flush();

        $defaultContext = [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['user']
        ];

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer(null, null, null, null, null, null, $defaultContext)];

        $serializer = new Serializer($normalizers, $encoders);

        return new JsonResponse($serializer->serialize($userAddress, 'json'));
    }

     /**
     * @Route("/user/address/edit", name="user_address_edit")
     */
    public function edit()
    {
        $request = Request::createFromGlobals();

        $userAddress = $this->getDoctrine()->getRepository(UserAddress::class)->findOneBy(['user' => $this->getUser()]);

        $entityManager = $this->getDoctrine()->getManager();

        $userAddress->setName($request->request->get('user_address')['name']);
        $userAddress->setSurname($request->request->get('user_address')['surname']);
        $userAddress->setAddress($request->request->get('user_address')['address']);
        $userAddress->setZipCode($request->request->get('user_address')['zip_code']);
        $userAddress->setCity($request->request->get('user_address')['city']);
        $userAddress->setCountry($request->request->get('user_address')['country']);
        $userAddress->setPhoneNumber($request->request->get('user_address')['phone_number']);

        $entityManager->persist($userAddress);
        $entityManager->flush();

        $defaultContext = [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['user']
        ];

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer(null, null, null, null, null, null, $defaultContext)];

        $serializer = new Serializer($normalizers, $encoders);

        return new JsonResponse($serializer->serialize($userAddress, 'json'));
    }
}
