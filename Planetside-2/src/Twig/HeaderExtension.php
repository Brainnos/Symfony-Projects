<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\Environment;

use App\Repository\ArticlesRepository;
use App\Repository\SubTagsRepository;
use App\Repository\TagsRepository;

class HeaderExtension extends AbstractExtension
{
    private $twig;
    private $articlesRepo;

    public function __construct(Environment $twig, ArticlesRepository $articlesRepo, SubTagsRepository $subTagsRepo)
    {
        $this->twig = $twig;
        $this->articlesRepo = $articlesRepo;
        $this->subTagsRepo = $subTagsRepo;
    }

    public function getFunctions(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            new TwigFunction('displayHeader', [$this, 'displayHeader'], ['is_safe' => ['html']]),
        ];
    }

    public function displayHeader()
    {

        $classes = $this->articlesRepo->findBy(['tag' => 3]);


        return $this->twig->render('front/header.html.twig', [
            'classes' => $classes,
            ]);
    }
}
