<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Article;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\ArticleRepository;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Categorie;




class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(ArticleRepository $articleRepository): response
    {
        $articles = $articleRepository->findAll();
        dump($articles);
        return $this->render('article/index.html.twig', ['controller_name' => 'ArticleController', 'articles' => $articles]);
    }

    //     public function upload()
    // {

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $someNewFilename = ...

    //         $file = $form['attachment']->getData();
    //         $file->move($directory, $someNewFilename);

    //     }

    // }

    ////////////////////////////////////////////////////////////////////////////////////////
    #[Route('/createArticle', name: 'create_article')]

    public function new(Request $request, PersistenceManagerRegistry $doctrine)
    {
        $form = $this->createFormBuilder()
            ->add('nomArticle', TextType::class)
            // $builder->add('attachment', FileType::class);
            ->add('imageArticle', FileType::class)
            ->add('prixArticle', TextType::class)
            ->add('descriptionArticle', TextType::class)
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nomCategorie',
                'choice_value' => 'id',
            ])
            ->add('save', SubmitType::class, ['label' => 'Créer article'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $form->getData();

            $article = new Article();
            $article->setNomArticle($form->getData()['nomArticle']);
            // $article->setImageArticle($form->getData()['imageArticle']);
            $article->setPrixArticle($form->getData()['prixArticle']);
            $article->setDescriptionArticle($form->getData()['descriptionArticle']);
            $article->setCategorie($form->getData()['categorie']);

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
