<?php

namespace App\Controller;

use App\Entity\UserOrder;
use App\Form\AddressType;
use App\Form\ProfileType;
use App\Repository\AddressRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }

    #[Route('/profile/orders', name: 'app_profile_orders')]
    public function orders(): Response
    {
        return $this->render('profile/orders.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }

    #[Route('/profile/orders/{id}/details', name: 'app_profile_order_show')]
    public function orderDetails(UserOrder $order): Response
    {
        return $this->render('profile/order_show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/profile/update', name: 'app_profile_update')]
    public function profileUpdate(Request $request, UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        $profileForm = $this->createForm(ProfileType::class, $user);
        $profileForm->handleRequest($request);

        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            $userRepository->add($user, true);
            $this->addFlash('success', 'Votre profil a bien été mis à jour.');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/profile_update.html.twig', [
            'profileForm' => $profileForm->createView(),
        ]);
    }

    #[Route('/profile/address/add', name: 'app_profile_address_add')]
    public function addAddress(
        Request $request,
        AddressRepository $addressRepository,
        UserRepository $userRepository
        ): Response
    {
        $addressForm = $this->createForm(AddressType::class);
        $addressForm->handleRequest($request);

        if ($addressForm->isSubmitted() && $addressForm->isValid()) {
            $address = $addressForm->getData();
            $address->setUser($this->getUser());
            
            if (!$this->getUser()->getMainAddress()) {
                $this->getUser()->setMainAddress($address);
            }
            $addressRepository->add($address, true);
            $userRepository->add($this->getUser(), true);
            $this->addFlash('success', 'Votre nouvelle adresse a bien été enregistrée.');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/address_form.html.twig', [
            'addressForm' => $addressForm->createView(),
        ]);
    }
}
