<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\UserOrder;
use App\Form\OrderType;
use App\Repository\ArticleRepository;
use App\Repository\ReferenceRepository;
use App\Repository\TicketRepository;
use App\Repository\UserOrderRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(
        Request $request,
        CartService $cartService,
        UserOrderRepository $userOrderRepository,
        TicketRepository $ticketRepository,
        ArticleRepository $articleRepository
    ): Response {
        $orderForm = $this->createForm(OrderType::class);
        $orderForm->handleRequest($request);

        if ($orderForm->isSubmitted() && $orderForm->isValid()) {
            if (!$this->getUser()) {
                return $this->redirectToRoute('app_login');
            }
            if (!$this->getUser()->getFirstname() || !$this->getUser()->getLastname()) {
                return $this->redirectToRoute('app_profile_update');
            }
            if (!$this->getUser()->getMainAddress()) {
                return $this->redirectToRoute('app_profile_address_add');
            }

            $order = new UserOrder();
            $order->setUser($this->getUser());
            $order->setTotal($cartService->getTotal());
            $userOrderRepository->add($order, true);

            $items = $cartService->getItems();
            foreach ($items as $item) {
                $detail = new Ticket();
                $detail->setReferenceOrder($order)
                ->setArticle($item['article'])
                ->setQty($item['qty']);
                $ticketRepository->add($detail, true);

                $item['article']->setQty($item['article']->getQty() - $item['qty']);
                $articleRepository->add($item['article'], true);
            }

            $cartService->cleanCart();
            $this->addFlash('success', 'Votre commande a bien été enregistrée !');
            return $this->redirectToRoute('app_profile_orders');
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
        ReferenceRepository $referenceRepository
    ): Response
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
