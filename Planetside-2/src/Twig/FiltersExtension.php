<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FiltersExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('prix', [$this, 'prix']),
            new TwigFilter('prenomAge', [$this, 'prenomAge']),
        ];
    }

    public function prix($prix)
    {
        return number_format($prix, 2, ',', ' ') . ' €';
    }

    public function prenomAge($prenom, $age, $ville)
    {
        return "Je m'appelle $prenom et j'ai $age ans et j'habite à $ville.";
    }
}
