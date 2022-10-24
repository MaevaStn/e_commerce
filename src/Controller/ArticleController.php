<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Article;
use App\Form\ArticleFormType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleRepository;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;


class ArticleController extends AbstractController
{

    #[Route('/article', name: 'app_article')]
    public function index(ArticleRepository $articleRepository): Response
    {

        $articles = $articleRepository->findAll();
        dump($articles);
        return $this->render('article/index.html.twig', ['controller_name' => 'ArticleController', 'articles' => $articles]);
    }

    #[Route('/createArticle', name: 'create_article')]

    public function new(Request $request, PersistenceManagerRegistry $doctrine, SluggerInterface $slugger)
    {
        $article = new Article();
        $form = $this->createForm(ArticleFormType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $form->getData();

            $article->setNomArticle($form->get('nomArticle')->getData());
            $article->setPrixArticle($form->get('prixArticle')->getData());
            $article->setDescriptionArticle($form->get('descriptionArticle')->getData());
            $article->setCategorie($form->get('categorie')->getData());
            // on récupére image et on l'ajoute au niveau de notre formulaire
            $imageFile = $form->get('img')->getData();


            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imageFile->move(
                        $this->getParameter('articles_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $article->setImageFileName($newFilename);
            }

            $entityManager = $doctrine->getManager();
            $entityManager->persist($article);
            $entityManager->flush();
            dump($entityManager);
            return $this->redirectToRoute('article_success');
        }

        return $this->render('createArticle/index.html.twig', array(
            'form' => $form->createView(), 'controller_name' => 'ArticleController',
        ));
    }


    #[Route("/success", name: "article_success")]

    public function affiche_result(Request $request)
    {
        return $this->render('article/success.html.twig', array(
            'article' => 'L\'article a bien été crée',
        ));
    }
}
