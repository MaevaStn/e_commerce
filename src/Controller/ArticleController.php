<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\ArticleRepository;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Categorie;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Service\FileUploader;
// use Symfony\Component\HttpFoundation\BinaryFileResponse;
// use Symfony\Component\HttpFoundation\JsonResponse;

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

    public function new(Request $request, SluggerInterface $slugger, PersistenceManagerRegistry $doctrine)
    {
        $form = $this->createFormBuilder()
            ->add('nomArticle', TextType::class)
            ->add('prixArticle', TextType::class)
            ->add('descriptionArticle', TextType::class)
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nomCategorie',
                'choice_value' => 'id',
            ])
            ->add('photo', FileType::class, [
                'label' => 'image article',
                //// unmapped means that this field is not associated to any entity property
                // pas de champp associé au fichier de notre entity : (il n'y a pas de champ qui s'appelle photo)
                'mapped' => false,
                //     // make it optional so you don't have to re-upload the PDF file
                //     // every time you edit the Product details
                'required' => false,
                //     // unmapped fields can't define their validation using annotations
                //     // in the associated entity, so you can use the PHP constraint classes
                // j'ajoute des contraintes :
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/gif',
                            'image/jpeg',
                            'image/jpg'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Image',
                    ])
                ],
            ])
            ->add('save', SubmitType::class, ['label' => 'Créer article'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $form->getData();

            $article = new Article();
            $article->setNomArticle($form->getData()['nomArticle']);
            // Faire une classe Photo ?

            $article->setPrixArticle($form->getData()['prixArticle']);
            $article->setDescriptionArticle($form->getData()['descriptionArticle']);
            // on récupére photo et on l'ajoute au niveau de notre formulaire
            $photo = $form->get('photo')->getData();
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



// $product->setBrochureFilename(
//     new File($this->getParameter('brochures_directory').'/'.$product->getBrochureFilename())
// );

// Vous pouvez utiliser le code suivant pour créer un lien vers l'image d'un produit :
// <a href="{{ asset('uploads/articless/' ~ product.brochureFilename) }}">View brochure (PDF)</a>

// //Récupération du dossier racine grace au kernel et ensuite ajout de l'emplacement du 
        // //fichier
        // $filename = $this->getParameter('Kernel.project_dir') . '/image/' . $imageArticle;
        // //Si le fichier existe alors on le renvoi, sinon retour 404
        // if (file_exists($filename)) {
        //     //retour d'un new BinaryFileResponse avec le nom du fichier
        //     return new BinaryFileResponse($filename);
        // } else {
        //     return new JsonResponse(null, 404);
        // }