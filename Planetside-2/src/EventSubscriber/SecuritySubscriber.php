<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

use App\Entity\Users;

class SecuritySubscriber implements EventSubscriberInterface
{

    private $tokenStorage;
    private $router;

    public function __construct(TokenStorageInterface $tokenStorage, UrlGeneratorInterface $router)
    {
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
    }


    public function onKernelRequest($event)
    {
        $token = $this->tokenStorage->getToken(); // On récupère le token de la session en cours
        if ($token) {
            $user = $token->getUser(); // On récupère le user via le token de session
            if ($user instanceof Users) { // On vérifie que le user est bien connecté
                if ($user->getActive() != 1) { // Si il n'est pas active
                    $response = new RedirectResponse($this->router->generate('app_logout'));
                    $event->setResponse($response);
                }
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'onKernelRequest',
        ];
    }
}
