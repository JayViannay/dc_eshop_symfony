<?php

namespace App\Service;

use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private $requestStack;
    private $articleRepository;

    public function __construct(RequestStack $requestStack, ArticleRepository $articleRepository)
    {
        $this->requestStack = $requestStack;
        $this->articleRepository = $articleRepository;
    }

    public function add(int $id, int $qty)
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        if (array_key_exists($id, $cart)) {
            $cart[$id] += $qty;
        } else {
            $cart[$id] = $qty;
        }
        $this->requestStack->getSession()->set('cart', $cart);
    }

    public function remove(int $id)
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        if (array_key_exists($id, $cart)) {
            unset($cart[$id]);
        }
        $this->requestStack->getSession()->set('cart', $cart);
    }

    public function getItems()
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        $items = [];
        foreach ($cart as $id => $qty) {
            $items[] = [
                'article' => $this->articleRepository->find($id),
                'qty' => $qty,
            ];
        }
        return $items;
    }

    public function getTotal()
    {
        $total = 0;
        foreach ($this->getItems() as $item) {
                $total += $item['article']->getRef()->getPrice()->getAmount() * $item['qty'];
            }
        return $total;
    }

    public function cleanCart()
    {
        $this->requestStack->getSession()->set('cart', []);
    }
}