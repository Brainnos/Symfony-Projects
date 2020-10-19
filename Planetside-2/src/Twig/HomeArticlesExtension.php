<?php

namespace App\Twig;

use App\Repository\ArticlesRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\Environment;


class HomeArticlesExtension extends AbstractExtension
{

    private $twig;
    private $articleRepo;

    public function __construct(Environment $twig, ArticlesRepository $articleRepo)
    {
        $this->twig = $twig;
        $this->articleRepo = $articleRepo;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('displayHomeArticles', [$this, 'displayHomeArticles'], ['is_safe' => ['html']]),
        ];
    }

    public function displayHomeArticles()
    {

        $lastArticles = $this->articleRepo->getLastArticles(6);
        return $this->twig->render('front/home_articles.html.twig', [
            'lastArticles' => $lastArticles,
        ]);
    }
}
