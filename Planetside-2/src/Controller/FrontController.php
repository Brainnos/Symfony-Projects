<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Service\EmailService;

use App\Entity\ContactPros;

use App\Repository\ArticlesRepository;

use App\Form\ContactProsType;
use App\Repository\TagsRepository;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Contracts\Cache\CacheInterface;

class FrontController extends AbstractController
{

    /**
     * @Route("/", name="accueil")
     */
    public function accueil(ArticlesRepository $articlesRepo, Request $request, CacheInterface $cache, TagsRepository $tagsRepo)
    {

        $tags = $tagsRepo->findAll();
        
        return $this->render('front/accueil.html.twig', [
            'tags' => $tags,
        ]);
    }


    /**
     * @Route("/les-crud", name="lesCrud")
     */
    public function lesCrud()
    {
        return $this->render('front/les-crud.html.twig', []);
    }
}