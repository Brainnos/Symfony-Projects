<?php

namespace App\Controller;

use App\Entity\Armes;
use App\Form\ArmesType;
use App\Repository\ArmesRepository;
use App\Repository\SubTagsRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/armes")
 */
class ArmesController extends AbstractController
{
    /**
     * @Route("/", name="armes_index", methods={"GET"})
     * @IsGranted("ROLE_STAFF")
     */
    public function index(ArmesRepository $armesRepository, SubTagsRepository $subTagsRepo): Response
    {

        $subTags = $subTagsRepo->findBy(["sous_categorie" => "Armes"]);

        return $this->render('armes/index.html.twig', [
            'armes' => $armesRepository->findAll(),
            'subTags' => $subTags,
        ]);
    }

    /**
     * @Route("/new", name="armes_new", methods={"GET","POST"})
     * @IsGranted("ROLE_STAFF")
     */
    public function new(Request $request): Response
    {
        $arme = new Armes();
        $form = $this->createForm(ArmesType::class, $arme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $arme->setActive(1);
            $entityManager->persist($arme);
            $entityManager->flush();

            return $this->redirectToRoute('armes_index');
        }

        return $this->render('armes/new.html.twig', [
            'arme' => $arme,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="armes_show", methods={"GET"})
     */
    public function show(Armes $arme): Response
    {
        return $this->render('armes/show.html.twig', [
            'arme' => $arme,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="armes_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_STAFF")
     */
    public function edit(Request $request, Armes $arme): Response
    {
        $form = $this->createForm(ArmesType::class, $arme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            $arme
            ->setDateUpdate(new DateTime())
            ->addUser($this->getUser());

            $em->flush();

            return $this->redirectToRoute('armes_index');
        }

        return $this->render('armes/edit.html.twig', [
            'arme' => $arme,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="armes_delete", methods={"DELETE"})
     * @IsGranted("ROLE_STAFF")
     */
    public function delete(Request $request, Armes $arme): Response
    {
        if ($this->isCsrfTokenValid('delete'.$arme->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $arme->setActive(0);
            $entityManager->flush();
        }

        return $this->redirectToRoute('armes_index');
    }
}
