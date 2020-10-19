<?php

namespace App\Controller;

use App\Entity\Armes;
use App\Entity\Articles;
use App\Entity\Vehicules;
use App\Repository\ArmesRepository;
use App\Repository\ArticlesRepository;
use App\Repository\SubTagsRepository;
use App\Repository\TagsRepository;
use App\Repository\VehiculesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Routing\Annotation\Route;

class SectionController extends AbstractController
{
    /**
     * @Route("/Guides-du-débutant", name="guidesDebutant")
     */

    public function guidesDebutant(ArticlesRepository $articlesRepo)
    {

        $articles = $articlesRepo->getArticlesBytag(1);

        return $this->render('section/guidesDebutant.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/Les-guides", name="guides")
     */

    public function guides(ArticlesRepository $articlesRepo)
    {

        $articles = $articlesRepo->getArticlesBytag(2);

        return $this->render('section/guides.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/Les-classes", name="classes")
     */

    public function classes(ArticlesRepository $articlesRepo)
    {

        $articles = $articlesRepo->getArticlesBytag(3);

        return $this->render('section/classes.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/Les-armes", name="armes")
     */

    public function armes(SubTagsRepository $subTagsRepo)
    {

        // On cherche les articles avec la sous catégorie "armes"
        $subTags = $subTagsRepo->findBy(['sous_categorie' => 'Armes']);

        return $this->render('section/armes.html.twig', [
            'subTags' => $subTags,
        ]);
    }


    /**
     * @Route("/Les-armes/{slug}", name="subArmes")
     */

    public function subArmes($slug, SubTagsRepository $subTagsRepo, ArmesRepository $armesRepo)
    {

        // On utilise le slug pour le titre de la page
        $title = $slug;

        // On trouve les sous tags en fonction du slug de la page (disponible grace à la variable subTag de la page armes) 
        $subTag = $subTagsRepo->findOneBy(['nom' => $slug]);

        // On cherche les articles ayant ce sous tag
        $armes = $armesRepo->findBy(['item_category_id' => $subTag->getId()]);

        $result = array();

        foreach ($armes as $arme) {
            $faction = $arme->getFactionId();

            switch ($faction) {
                case 1:
                    $result["VS"][] = $arme;
                    break;
                case 2:
                    $result["NC"][] = $arme;
                    break;
                case 3:
                    $result["TR"][] = $arme;
                    break;
                case 4:
                    $result["NS"][] = $arme;
                    break;
            }
        }

        return $this->render('sous-section/subArmes.html.twig', [
            'title' => $title,
            'result' => $result,
        ]);
    }

    /**
     * @Route("/Les-véhicules", name="vehicules")
     */

    public function vehicules(SubTagsRepository $subTagsRepo, VehiculesRepository $vehiculesRepo, TagsRepository $tagsRepo)
    {
        $subTags = $subTagsRepo->findBy(['sous_categorie' => 'Véhicules']);
        $vehicules = $vehiculesRepo->findAll();

        return $this->render('section/vehicules.html.twig', [
            'subTags' => $subTags,
            'vehicules' => $vehicules,
        ]);
    }

    /**
     * @Route("/Les-équipements", name="equipements")
     */
    public function equipements(SubTagsRepository $subTagsRepo)
    {
        $subTags = $subTagsRepo->findBy(['sous_categorie' => 'Equipements']);

        return $this->render('section/equipements.html.twig', [
            'subTags' => $subTags,
        ]);
    }

    /**
     * @Route("/Les-équipements/{slug}", name="subEquipements")
     */
    public function subEquipements($slug, SubTagsRepository $subTagsRepo, ArticlesRepository $articlesRepo)
    {

        // On utilise le slug pour le titre de la page
        $title = $slug;

        // On trouve les sous tags en fonction du slug de la page (disponible grace à la variable subTag de la page armes) 
        $subTag = $subTagsRepo->findOneBy(['nom' => $slug]);

        // On cherche les articles ayant ce sous tag
        $articles = $articlesRepo->findBy(['subTag' => $subTag->getId()]);


        return $this->render('sous-section/subEquipements.html.twig', [
            'title' => $title,
            'articles' => $articles,
        ]);
    }



    // public function appelApi() {
    // $em = $this->getDoctrine()->getManager();

    // $client = HttpClient::create();
    // $response = $client->request('GET', "http://census.daybreakgames.com/s:Brainnos/get/ps2:v2/item?c:case=false&item_category_id=2&c:start=5&c:limit=100&c:show=item_category_id,item_id,name.en,name.fr,description.fr,faction_id,image_id,image_path");
    // $content = $response->toArray();
    // $results = $content["item_list"];

    // foreach ($results as $result) {
    //     $arme = new Armes;

    // $name = $result["name"]["en"];
    // $name_fr = $result["name"]["fr"];

    // $description = $result["description"]["fr"];
    //     $arme
    //         ->setName($name)
    //         ->setItemId($result["item_id"])
    //         ->setItemCategoryId($result["item_category_id"])
    //         ->setDescription($description)
    //         ->setImageId($result["image_id"])
    //         ->setImagePath($result["image_path"])
    //         ->setNameFr($name_fr)
    //         ;
    //     if (empty($result["faction_id"])) {
    //         $arme->setFactionId(4);
    //     } else {
    //         $arme->setFactionId($result["faction_id"]);
    //     }
    //     $em->persist($arme);
    //     $em->flush();

    // }
    // }

    // public function createMassArticle(ArmesRepository $armesRepo) {
    // $tag = $tagsRepo->findOneBy(['id' => 4]);
    // $em = $this->getDoctrine()->getManager();
    // $all = $armesRepo->findAll();
    // foreach($all as $arme) {
    //     $subTag = $subTagsRepo->findOneBy(['id' => $arme->getItemCategoryId()]);
    //     $article = new Articles;
    //     $article
    //         ->setTitle($arme->getNameFr())
    //         ->setText("A écrire")
    //         ->setDateCreation(new \DateTime())
    //         ->setActive(0)
    //         ->setUser($this->getUser())
    //         ->setArme($arme)
    //         ->setTag($tag)
    //         ->setSubTag($subTag)
    //         ;
    //         $em->persist($article);
    //         $em->flush();

    // }

    // }

}
