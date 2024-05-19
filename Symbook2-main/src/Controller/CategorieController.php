<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Form\CategorieType;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategorieController extends AbstractController
{
    #[Route('/admin/categorie', name: 'admin_categorie')]
    public function index(CategoriesRepository $rep): Response
    {
        $categories = $rep->findAll();
        return $this->render('categorie/index.html.twig', [
            'categories' => $categories,
        ]);
    }
    #[Route('/admin/categorie/create', name: 'admin_categorie_create')]
    public function create(EntityManagerInterface $em, Request $request): Response
    {
        $categorie = new Categories();
        //construction de l'objet formulaire
        $form = $this->createForm(CategorieType::class, $categorie);
        // recupéretaion et traitement de données
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $em->persist($categorie);
            $em->flush();
            return $this->redirectToRoute('admin_categorie');
        }

        return $this->render('categorie/create.html.twig', [
            'f' => $form

        ]);
    }
    #[Route('/admin/categorie/update/{id}', name: 'admin_categorie_update')]
    public function update(Categories $categorie, EntityManagerInterface $em, Request $request): Response
    {
        //construction de l'objet formulaire
        $form = $this->createForm(CategorieType::class, $categorie);
        // recupéretaion et traitement de données
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            // $formData = $form->getData();
            //dd($formData->getLibelle());
            $em->persist($categorie);
            $em->flush();
            return $this->redirectToRoute('admin_categorie');
        }

        return $this->render('categorie/update.html.twig', [
            'f' => $form

        ]);
    }

    
  

    #[Route('/admin/categorie/delete/{id}', name: 'admin_categorie_delete')]
    public function delete(Categories $categorie, EntityManagerInterface $em): Response
    {
        $em->remove($categorie);
        $em->flush();
        return $this->redirectToRoute('admin_categorie');
    }
}
