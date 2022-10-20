<?php

namespace App\Controller;

use App\Entity\Categorie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\CategorieRepository;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CategorieController extends AbstractController
{
    #[Route('/categorie', name: 'app_categorie')]
    public function index(CategorieRepository $categorieRepository): Response
    {
        $categories = $categorieRepository->findAll();
        return $this->render('categorie/index.html.twig', [
            'controller_name' => 'CategorieController', 'categories' => $categories
        ]);
    }

    /////////////////////////////////////////////
    #[Route('/createCategorie', name: 'create_categorie')]

    public function new(Request $request, PersistenceManagerRegistry $doctrine)

    {

        $form = $this->createFormBuilder()
            ->add('nomCategorie', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Créer catégorie'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $form->getData();

            $categorie = new Categorie();
            $categorie->setNomCategorie($form->getData()['nomCategorie']);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($categorie);
            $entityManager->flush();

            return $this->redirectToRoute('categorie_sucess');
        }
        return $this->render('createCategorie/index.html.twig', array(
            'form' => $form->createView(), 'controller_name' => 'CategorieController',
        ));
    }

    #[Route("/sucess", name: "categorie_sucess")]

    public function affiche_result(Request $request)
    {
        return $this->render('categorie/sucess.html.twig', array(
            'categorie' => 'Vous avez ajouter la catégorie :'
        ));
    }
}
