<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ApiController extends AbstractController
{
    #[Route('/reference/size/colors', name: 'api_ref_size_get_color')]
    public function getColorsByRefAndSize(Request $request, ArticleRepository $articleRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        $articles = $articleRepository->findByReferenceAndSize($data['ref_id'], $data['size_id']);

        $colors = [];
        foreach ($articles as $article) {
            if (!in_array($article->getColor()->getId(), $colors)) {
                $colors[$article->getColor()->getId()] = $article->getColor()->getName();
            }
        }

        $response = new Response(json_encode(array('colors' => $colors)));
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }

    #[Route('/reference/article-qty', name: 'api_ref_article_qty')]
    public function getArticleQty(Request $request, ArticleRepository $articleRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        $article = $articleRepository->findOneByParams($data['ref_id'], $data['size_id'], $data['color_id']);

        $response = new Response(json_encode(array('article_qty' => $article->getQty())));
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
}
