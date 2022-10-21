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
// le bon :
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Categorie;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(ArticleRepository $articleRepository): response
    {
        $articles = $articleRepository->findAll();
        dump($articles);
        return $this->render('article/index.html.twig', ['controller_name' => 'ArticleController', 'articles' => $articles]);
    }

    ////////////////////////////////////////////////////////////////////////////////////////
    #[Route('/createArticle', name: 'create_article')]

    public function new(Request $request, PersistenceManagerRegistry $doctrine, SluggerInterface $slugger)
    {
        $form = $this->createFormBuilder()
            ->add('nomArticle', TextType::class)
            // j'utilise un FileType , permet d'avoir un input pour uploader le fichier en question
            // resultat qui ne correspond pas au niveau de notre formulaire donc :
            // et donc ajout de champ unmapped ensuite et mettre à jour ce champ ensuite av getters et setters
            ->add('photo', FileType::class, [
                'label' => 'imageArticle',
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
            // on récupére photo et on l'ajoute au niveau de notre formulaire
            $photo = $form->get('photo')->getData();
            $article->setPrixArticle($form->getData()['prixArticle']);
            $article->setDescriptionArticle($form->getData()['descriptionArticle']);
            $article->setCategorie($form->getData()['categorie']);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($article);
            ///////////////////////////////////////////////

            // verifier si on a uploader une image (photo) :
            if ($photo) {
                // si oui , va me récupérer l'originalName
                // puis, création d'un nom de fichier :
                // renommage du fichier :
                // detection du nom original de notre dossier : (originalFilename)
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                // création du slug associé à notre originalFilname
                $safeFilename = $slugger->slug($originalFilename);
                // récupération du nouveau Filename av un id unique (généré au moment ou j'appelle la méthode) et l'extension de la méthode :
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photo->guessExtension();

                // Move the file to the directory where brochures are stored
                // va aller copier le contenu du fichier temporaire que je vais uploader et le stocker ds l'application
                try {
                    // va faire un move du newFilename vers cet endroit :  $this->getParameter('brochures_directory'), et cet endroit se trouve dans param au niveau de service.yaml,
                    // donc aller le créer en modifiant le path
                    $photo->move(
                        // va me chercher le path du dossier vers lequel je dois uploader mon image et place le ici
                        $this->getParameter('articles_directory'),
                        $newFilename
                    );
                    // si problème avec affichage :
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                // objet article $article, récupère le newFilename et ajoute le à cette image
                $article->setBrochureFilename($newFilename);

                // return $this->redirectToRoute('app_article_list');
            }
            //////////////////////////////////////////////////////////////////////////////////
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
