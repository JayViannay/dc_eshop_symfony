<?php

namespace App\Controller;

use App\Form\AddressType;
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

    #[Route('/profile/address/add', name: 'app_profile_address_add')]
    public function addAddress(Request $request): Response
    {
        $addressForm = $this->createForm(AddressType::class);
        $addressForm->handleRequest($request);

        if ($addressForm->isSubmitted() && $addressForm->isValid()) {
            dd('post address');
        }

        return $this->render('profile/address_form.html.twig', [
            'addressForm' => $addressForm->createView(),
        ]);
    }
}
