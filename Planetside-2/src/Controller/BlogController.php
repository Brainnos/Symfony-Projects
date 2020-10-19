<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Articles;
use App\Entity\Comments;
use App\Entity\Tags;


use App\Repository\ArticlesRepository;

use App\Form\ArticlesType;
use App\Repository\TagsRepository;
use Exception;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class BlogController extends AbstractController
{
    /**
     * @Route("/staff/article-crud/{id}", name="article_crud")
     */
    public function article_crud($id, Request $request, ArticlesRepository $articlesRepo, CacheInterface $cache, TagsRepository $tagsRepo, SluggerInterface $slugger)
    {
        $this->denyAccessUnlessGranted('ROLE_STAFF');

        $em = $this->getDoctrine()->getManager();
        
        // Si id == 0 : nouvel article, Sinon on récupère l'article en BDD
        if ($id == 0) {
            $article = new Articles();
            $nouveau = true;
        } else {
            $article = $articlesRepo->find($id);
            if (!$article) {
                $this->addFlash('danger', "Cet article n'a pas été trouvé.");
                return $this->redirectToRoute('articles');
            }

            $nouveau = false;
        }

        // Supprimer un article
        $action = $request->query->get('action');
        if ($action == 'delete') {
            $article->setActive(0);
            $em->flush();

            $this->addFlash('warning', "L'article a bien été supprimé.");
            return $this->redirectToRoute('articles');
        }


        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('image')->getData();
            $article = $form->getData();
            if ($nouveau) {
                $article
                    ->setDateCreation(new \DateTime())
                    ->setActive(1)
                    ->setUser($this->getUser());
            }

            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $image->move(
                        $this->getParameter('image'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $article->setImage($newFilename);
            }

            $em->persist($article);
            $em->flush();

            $this->addFlash('success', "L'article a bien été " . ($nouveau ? 'créé' : 'modifié') . ".");
            return $this->redirectToRoute('article_crud', ['id' => $article->getId()]);
        }

        return $this->render('blog/article_crud.html.twig', [
            'form' => $form->createView(),
            'nouveau' => $nouveau,
            'article' => $article,
        ]);
    }

    /**
     * @Route("/articles", name="articles")
     */
    public function articles(ArticlesRepository $articlesRepo, TagsRepository $tagsRepo)
    {

        $this->denyAccessUnlessGranted("ROLE_STAFF");
        // Requete pour récupérer tous les articles
        $articles = $articlesRepo->findAll();
        $tags = $tagsRepo->findAll();

        return $this->render('blog/articles.html.twig', [
            'articles' => $articles,
            'tags' => $tags,
        ]);
    }

    /**
     * @Route("/article/{id}", name="article")
     */
    public function article($id, Request $request)
    {

        // On récupère l' ArticlesRepository
        $em = $this->getDoctrine()->getManager();
        $articlesRepo = $em->getRepository(Articles::class);

        // On récupère l'article, en fonction de l'ID qui est dans l'URL
        $article = $articlesRepo->find($id);

        if (!$article) {
            $this->addFlash('danger', "L'article demandé n'a pas été trouvé.");
            return $this->redirectToRoute('accueil');
        }

        if ($article->getActive() == 0) {
            $this->addFlash('danger', "L'article demandé n'a pas été trouvé.");
            return $this->redirectToRoute('accueil');
        }

        return $this->render('blog/article.html.twig', [
            'article' => $article
        ]);
    }
}
