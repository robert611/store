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
use App\Form\UserAddressType;

/**
 * @IsGranted("ROLE_USER")
 */
class UserAddressController extends AbstractController
{
    /**
     * @Route("api/user/address/new", name="api_user_address_new")
     */
    public function apiNew()
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
     * @Route("api/user/address/edit", name="api_user_address_edit")
     */
    public function apiEdit()
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

    /**
     * @Route("user/address/{id}/edit", name="user_address_edit")
     */
    public function edit(Request $request, UserAddress $userAddress)
    {
        $form = $this->createForm(UserAddressType::class, $userAddress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($userAddress);
            $entityManager->flush();

            return $this->redirectToRoute('user_address_edit', ['id' => $userAddress->getId()]);
        }

        return $this->render('account/edit_user_address.html.twig',
            ['form' => $form->createView()]);
    }

    /**
     * @Route("api/user/address/get", name="user_address_get")
     */
    public function doesUserAddressExist()
    {
        $userAddress = $this->getDoctrine()->getRepository(UserAddress::class)->findOneBy(['user' => $this->getUser()]);

        if ($userAddress) {
            return new JsonResponse(['answer' => true]);
        }

        return new JsonResponse(['answer' => false]);
    }
}
