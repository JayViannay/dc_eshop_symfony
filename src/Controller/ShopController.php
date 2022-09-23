<?php

namespace App\Controller;

use App\Entity\Reference;
use App\Repository\ReferenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{
    #[Route('/', name: 'app_shop')]
    public function index(ReferenceRepository $referenceRepository): Response
    {
        return $this->render('shop/index.html.twig', [
            'references' => $referenceRepository->findAll(),
        ]);
    }

    #[Route('/article/{slug}', name: 'app_shop_show')]
    public function show(Reference $reference): Response
    {
        return $this->render('shop/show.html.twig', [
            'reference' => $reference,
        ]);
    }

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('shop/about.html.twig');
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('shop/contact.html.twig');
    }
}
