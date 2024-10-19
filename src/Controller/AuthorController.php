<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    #[Route('/list_author', name: 'author_list')]
    public function showAuthors(AuthorRepository $authorRepo): Response
    {
        $authors = $authorRepo->findAll();
    
        return $this->render('author/list.html.twig', [
            'authors' => $authors,
        ]);
    }
    

    #[Route('/addone', name: 'addautherone')]
    public function addAutherManual(EntityManagerInterface $em): Response
    {
        $authorOne = new Author();
        $authorOne->setUsername('ali');
        $authorOne->setEmail('ali@esprit.tn');
        $em->persist($authorOne);
        $em->flush();
        return new Response('ajout avec succes');
    }
    //ajout d'un auteur a travers un formulaire (sa page twig est author/add.html.twig)
    /*
Ajout d'un Auteur à travers le formulaire
*/

    #[Route('/add-author2', name: 'add_author2')]
    public function addAuthor2(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        // Créer une nouvelle instance de Author
        $author = new Author();

        // Créer le formulaire
        $form = $this->createForm(AuthorType::class, $author);

        // Traitement de la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist (sauvegarder) l'auteur dans la base de données
            $entityManager->persist($author);
            $entityManager->flush(); // Envoie les changements à la base de données

            // Redirige vers la liste des auteurs ou une autre page
            return $this->redirectToRoute('author_list');
        }

        // Retourner la vue du formulaire
        return $this->render('author/add.html.twig', [
            'formxx' => $form->createView(), // Crée la vue du formulaire
        ]);
    }
    //la fonction edit (reliée avec la page author/edit.html.twig(mon choix))

    // src/Controller/AuthorController.php
    #[Route('/edit-author/{id}', name: 'edit_author')]
    public function editAuthor(
        Request $request,
        EntityManagerInterface $entityManager,
       int  $id
    ): Response {
        // Récupérer l'auteur à modifier
        $author = $entityManager->getRepository(Author::class)->find($id);

        if (!$author) {
            throw $this->createNotFoundException('Auteur non trouvé');
        }

        // Créer le formulaire
        $form = $this->createForm(AuthorType::class, $author);

        // Traitement de la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mettre à jour l'auteur dans la base de données
            $entityManager->flush(); // Envoie les changements à la base de données

            return $this->redirectToRoute('author_list'); // Redirige vers la liste des auteurs
        }

        // Retourner la vue du formulaire pour modifier l'auteur
        return $this->render('author/edit.html.twig', [
            'form' => $form->createView(),
            'author' => $author,
        ]);
    }

    //la fonction delete (ne necessite pas une page html.twig)
    // delete

    #[Route('/delete-author/{id}', name: 'delete_author')]
    public function deleteAuthor(EntityManagerInterface $entityManager,int $id): Response 
    {
        // Récupérer l'auteur à supprimer
        $author = $entityManager->getRepository(Author::class)->find($id);

        if (!$author) {
            throw $this->createNotFoundException('Auteur non trouvé');
        }

        // Supprimer l'auteur
        $entityManager->remove($author);
        $entityManager->flush(); // Envoie les changements à la base de données

        return $this->redirectToRoute('author_list'); // Redirige vers la liste des auteurs
    }
}
