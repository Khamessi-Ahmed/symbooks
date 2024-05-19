<?php

namespace App\Controller;

use DateTime;
use App\Entity\Livres;
use App\Form\LivreType;
use App\Repository\LivresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class LivresController extends AbstractController
{
   

    #[Route('/admin/livres', name: 'admin_livres')]
    public function index(LivresRepository $rep, Request $request): Response
    {
        $titre = $request->query->get('titre');
        $auteur = $request->query->get('auteur');
        $livres = $rep->search($titre, $auteur);

        return $this->render('livres/index.html.twig', [
            'livres' => $livres,
            'titre' => $titre,
            'auteur' => $auteur,
        ]);
    }

    #[Route('/admin/livres/show/{id}', name: 'admin_livres_show')]
    public function show(Livres $livre): Response
    {

        return $this->render('Livres/show.html.twig', ['livre' => $livre]);
    }
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/admin/livres/create', name: 'app_admin_livres_create')]
    public function create(EntityManagerInterface $em): Response
    {
        $livre1 = new Livres();
        $livre1->setAuteur('auteur 1')
            ->setEditedAt(new \DateTimeImmutable('01-01-2023'))
            ->setTitre('Titre 4')
            ->setQte(100)
            ->setResume('jhgkjhkjhlhdjfjfdgpghkgmgbkmgblkgm')
            ->setSlug('titre-4')
            ->setPrix(200)
            ->setEditeur('Eni')
            ->setISBN('111.1111.1111.1115')
            ->setImage('https://picsum.photos/300');
        $livre2 = new Livres();
        $livre2->setAuteur('auteur 3')
            ->setEditedAt(new \DateTimeImmutable('01-01-2023'))
            ->setTitre('Titre 4')
            ->setQte(100)
            ->setResume('jhgkjhkjhlhdjfjfdgpghkgmgbkmgblkgm')
            ->setSlug('titre-4')
            ->setPrix(200)
            ->setEditeur('Eni')
            ->setISBN('111.1111.1111.1115')
            ->setImage('https://picsum.photos/300');
        $em->persist($livre1);
        $em->persist($livre2);
        $em->flush();
        dd($livre1);
    }
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/admin/livres/delete/{id}', name: 'app_admin_livres_delete')]
    public function delete(EntityManagerInterface $em, Livres $livre): Response
    {

        $em->remove($livre);
        $em->flush();
        dd($livre);
    }
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/admin/livres/add', name: 'admin_livres_add')]
    public function add(EntityManagerInterface $em, Request $request): Response
    {
        $livre = new Livres();
        //construction de l'objet formulaire
        $form = $this->createForm(LivreType::class, $livre);
        // recupéretaion et traitement de données
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $em->persist($livre);
            $em->flush();
            return $this->redirectToRoute('admin_livres');
        }

        return $this->render('livres/add.html.twig', [
            'f' => $form

        ]);
    }
    #[Route('/admin/livres/update/{id}', name: 'admin_livres_update')]
    #[IsGranted("ROLE_ADMIN")]
    public function update(EntityManagerInterface $em, Request $request, Livres $livre): Response
    {
    
        $form = $this->createForm(LivreType::class, $livre);


        $form->handleRequest($request);

    
        if ($form->isSubmitted() && $form->isValid()) {
            
            $em->flush();

            
            return $this->redirectToRoute('admin_livres_show', ['id' => $livre->getId()]);
        }

        return $this->render('livres/update.html.twig', [
            'form' => $form->createView(),
            'livre' => $livre,
        ]);
    }




    #[IsGranted("ROLE_ADMIN")]
    #[Route('admin/livres/livreplusvendu', name: 'livresplusvendu')]
    
    public function livreplusvendu(EntityManagerInterface $em, Request $request, LivresRepository $livresRepo): Response
    {
       
        // Fetch the most sold books
        $books = $livresRepo->findMostSoldBooks();

        $bookTitles = [];
        $bookSales = [];

        foreach ($books as $book) {
            $bookTitles[] = $book['titre'];
            $bookSales[] = $book['sales'];
        }

        return $this->render('livres/livreplusvendu.html.twig', [
            'bookTitles' => json_encode($bookTitles),
            'bookSales' => json_encode($bookSales),
        ]);
    }
  
}
