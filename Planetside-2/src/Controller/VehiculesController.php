<?php

namespace App\Controller;

use App\Entity\Vehicules;
use App\Form\VehiculesType;
use App\Repository\SubTagsRepository;
use App\Repository\VehiculesRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * @Route("/vehicules")
 */
class VehiculesController extends AbstractController
{
    /**
     * @Route("/", name="vehicules_index", methods={"GET"})
     * @IsGranted("ROLE_STAFF")
     */
    public function index(VehiculesRepository $vehiculesRepository, SubTagsRepository $subTagsRepo): Response
    {

        $subTags = $subTagsRepo->findBy(["sous_categorie" => "Vehicules"]);

        return $this->render('vehicules/index.html.twig', [
            'vehicules' => $vehiculesRepository->findAll(),
            'subTags' => $subTags,
        ]);
    }

    /**
     * @Route("/new", name="vehicules_new", methods={"GET","POST"})
     * @IsGranted("ROLE_STAFF")
     */
    public function new(Request $request): Response
    {
        $vehicule = new Vehicules();
        $form = $this->createForm(VehiculesType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $vehicule->setActive(1);
            $entityManager->persist($vehicule);
            $entityManager->flush();

            $this->addFlash('success', "Le véhicule a bien été crée");
            return $this->redirectToRoute('vehicules_index');
        }

        return $this->render('vehicules/new.html.twig', [
            'vehicule' => $vehicule,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="vehicules_show", methods={"GET"})
     */
    public function show(Vehicules $vehicule): Response
    {
        return $this->render('vehicules/show.html.twig', [
            'vehicule' => $vehicule,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="vehicules_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_STAFF")
     */
    public function edit(Request $request, Vehicules $vehicule): Response
    {
        $form = $this->createForm(VehiculesType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            $vehicule
            ->setDateUpdate(new DateTime())
            ->addUser($this->getUser());

            $em->flush();
            

            return $this->redirectToRoute('vehicules_index');
        }

        return $this->render('vehicules/edit.html.twig', [
            'vehicule' => $vehicule,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="vehicules_delete", methods={"DELETE"})
     * @IsGranted("ROLE_STAFF")
     */
    public function delete(Request $request, Vehicules $vehicule): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vehicule->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $vehicule->setActive(0);
            $entityManager->flush();
        }

        return $this->redirectToRoute('vehicules_index');
    }
}
