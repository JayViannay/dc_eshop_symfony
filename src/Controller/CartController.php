<?php

namespace App\Controller;

use App\Form\OrderType;
use App\Repository\ArticleRepository;
use App\Repository\ReferenceRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(Request $request, CartService $cartService): Response
    {

        $orderForm = $this->createForm(OrderType::class);
        $orderForm->handleRequest($request);

        if ($orderForm->isSubmitted() && $orderForm->isValid()) {
            if (!$this->getUser()) return $this->redirectToRoute('app_login');
            if ($this->getUser()->getAddresses()->isEmpty()) return $this->redirectToRoute('app_profile_address_add');
            // user has an address & name + firstname

            // si tout est ok 
            // créer une nouvelle commande
            // cartService->getItems() => articles
            // pour chacun des articles dans le panier je créer un ticket
            // puis je redirige user ailleurs commande validé
            
            
            dd('action payer');
        }

        return $this->render('cart/index.html.twig', [
            'articles' => $cartService->getItems(),
            'total' => $cartService->getTotal(),
            'orderForm' => $orderForm->createView()
        ]);
    }

    #[Route('/cart/add', name: 'app_cart_add')]
    public function add(
        Request $request,
        ArticleRepository $articleRepository,
        CartService $cartService,
        ReferenceRepository $referenceRepository): Response
    {
        $ref = $request->get('ref_id');
        $color = $request->get('color');
        $size = $request->get('size');
        $qty = $request->get('qty');

        $article = $articleRepository->findOneByParams($ref, $size, $color);
        $reference = $referenceRepository->find($ref);
        if ($article) {
            $stock = $article->getQty();
            if ($stock >= $qty && $stock > 0) {
                $cartService->add($article->getId(), $qty);
                $this->addFlash('success', 'Article ajouté au panier !');
                return $this->redirectToroute('app_shop_show', ['slug' => $reference->getSlug()]);
            } else {
                $this->addFlash('danger', 'Stock insuffisant');
                return $this->redirectToroute('app_shop_show', ['slug' => $reference->getSlug()]);
            }
        }
    }

    #[Route('/cart/remove/{id}', name: 'app_cart_remove')]
    public function remove(int $id, CartService $cartService): Response
    {
        $cartService->remove($id);
        return $this->redirectToRoute('app_cart');
    }
}
