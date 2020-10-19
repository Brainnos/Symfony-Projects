<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Repository\UsersRepository;
use App\Security\UsersAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use DateTime;
use App\Service\EmailService;
use Exception;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('accueil');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
    }


    /**
     * @Route("/register-validate-email", name="register_validate_email")
     */
    public function register_validate_email(Request $request, UsersRepository $usersRepo)
    {

        $error = false;

        // Récupérer GET email - token
        $email = $request->query->get('email');
        $token = $request->query->get('token');

        // Chercher le user par son email
        $user = $usersRepo->findOneBy(['email' => $email]);

        if (!$user) {
            $this->addFlash('danger', "Votre adresse email ne correspond à aucun compte.");
        } elseif ($user->getActive() == 1) {
            $this->addFlash('danger', "Votre compte a déjà été activé, vous pouvez vous connecter.");
        } elseif ($token != $user->getToken()) {
            $this->addFlash('danger', "Une erreur est survenue. Contactez Brennos#1507 sur discord");
        } else {
            $user->setActive(1);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            // Redirige vers login avec flash
            $this->addFlash('success', "Votre compte a bien été validé, vous pouvez désormais vous connecter");

            $this->redirectToRoute('app_login');
        }


        return $this->render('emails/security/register_validate_email.html.twig', [
            'error' => $error,
        ]);
    }

    /**
     * @Route("/password-forgotten", name="password_forgotten")
     */
    public function password_forgotten(Request $request, UsersRepository $usersRepo, EmailService $emailService)
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $user = $usersRepo->findOneBy(['email' => $email]);

            if (!$user) {
                $this->addFlash('danger', "Votre adresse mail ne correspond à aucun compte.");
            } else {
                $user->setToken($this->generateToken());
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                $link = $this->generateUrl('password_update', ['email' => $user->getEmail(), 'token' => $user->getToken()], UrlGeneratorInterface::ABSOLUTE_URL);
                $emailService->password_forgotten($user, $link);

                $this->addFlash('success', "Nous vous avons envoyé un email contenant un lien pour modifier votre mot de passe.");
                return $this->redirectToRoute('password_forgotten');
            }
        }


        return $this->render('security/password_forgotten.html.twig', []);
    }

    /**
     * @Route("/password-update", name="password_update")
     */
    public function password_update(Request $request, UsersRepository $usersRepo, UserPasswordEncoderInterface $passwordEncoder)
    {
        
        $user = $this->getUser();
        $success = "Votre mot de passe a bien été modifié";
        if (!$user) {
            $email = $request->query->get('email');
            $token = $request->query->get('token');
            $user = $usersRepo->findOneBy(['email' => $email]);
    
            if (!$user || $token != $user->getToken()) {
                throw new Exception("Page interdite !");
            }    
            $success = "Votre mot de passe a bien été modifié, vous pouvez maintenant vous connecter";

        }

        $form = $this->createForm(RegistrationFormType::class, $user);

        $form
        ->remove('firstname')
        ->remove('lastname')
        ->remove('email')
        ->remove('agreeTerms');

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
                );
                $user->setToken($this->generateToken());                
                $user->setUpdatedAt(new DateTime());

                $em = $this->getDoctrine()->getManager();
                $em->flush();

                $this->addFlash('success', $success);
                return $this->redirectToRoute('app_login');
        }

        return $this->render('security/password_update.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    /**
     * @Route("/register", name="app_register")
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        GuardAuthenticatorHandler $guardHandler,
        UsersAuthenticator $authenticator,
        EmailService $emailService
    ): Response {
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $now = new DateTime();
            $user
                ->setCreatedAt($now)
                ->setUpdatedAt($now)
                ->setToken($this->generateToken())
                ->setActive(0);


            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // do anything else you need here, like send an email

            // envoyer un email de confirmation

            $emailService->register($user);



            // Rediriger avec message
            $this->addFlash(
                'success',
                'Votre inscription a bien été prise en compte. Mercie de cliquer sur le lien afin de finaliser votre inscription ' . $user->getEmail()
            );
            return $this->redirectToRoute('app_login', ['send' => "ok"]);


            // return $guardHandler->authenticateUserAndHandleSuccess(
            //     $user,
            //     $request,
            //     $authenticator,
            //     'main' // firewall name in security.yaml
            // );
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    private function generateToken()
    {
        return substr(bin2hex(random_bytes(50)), 0, 32);
    }
}
