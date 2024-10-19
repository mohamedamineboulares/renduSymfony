<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Form\BookType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BookController extends AbstractController
{
    #[Route('/book/controller/', name: 'app_book_controller_')]
    public function index(): Response
    {
        return $this->render('book_controller /index.html.twig', [
            'controller_name' => 'BookController Controller',
        ]);
    }

    // Afficher les Livres Publiés
    #[Route('/books/published', name: 'published_books')]
    public function publishedBooks(EntityManagerInterface $entityManager): Response 
    {
        // Récupérer tous les livres publiés
        $publishedBooks = $entityManager->getRepository(Book::class)->findBy(['published' => true]);

        // Récupérer tous les livres non publiés
        $unpublishedBooksCount = $entityManager->getRepository(Book::class)->count(['published' => false]);

        return $this->render('book/published.html.twig', [
            'publishedBooks' => $publishedBooks,
            'unpublishedBooksCount' => $unpublishedBooksCount,
        ]);
    }

    // Ajout Book
    #[Route('/book/add/', name: 'app_book_controller_2')]
    public function addBook(Request $request,EntityManagerInterface $entityManager): Response 
    {
        $book = new Book();
        $author = new Author();
        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrement de l'entité
            $entityManager->persist($book);
            $val = $book->getAuthor()->getNbBooks();
            $book->getAuthor()->setNbBooks($val + 1);
            $entityManager->flush();

            return $this->redirectToRoute('published_books'); // Redirige vers une route appropriée
        }

        return $this->render('book/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Edit BOOK
    #[Route('/book/edit/{id}', name: 'edit_book')]
    public function editBook(
        Request $request,
        EntityManagerInterface $entityManager,
        int $id
    ): Response {
        $book = $entityManager->getRepository(Book::class)->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé');
        }

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $val = $book->getAuthor()->getNbBooks();
            $book->getAuthor()->setNbBooks($val + 1);
            $entityManager->flush();
            return $this->redirectToRoute('published_books');
        }

        return $this->render('book/edit.html.twig', [
            'form' => $form->createView(),
            'book' => $book,
        ]);
    }

    // delete
    #[Route('/book/delete/{id}', name: 'delete_book')]
    public function deleteBook(
        EntityManagerInterface $entityManager,
        int $id
    ): Response {
        $book = $entityManager->getRepository(Book::class)->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé');
        }

        $entityManager->remove($book);
        $entityManager->flush();
        return $this->redirectToRoute('published_books');
    }

    // detail
    #[Route('/book/view/{id}', name: 'book_details')]
    public function viewBook(
        EntityManagerInterface $entityManager,
        int $id
    ): Response {
        $book = $entityManager->getRepository(Book::class)->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé');
        }

        return $this->render('book/view.html.twig', [
            'book' => $book,
        ]);
    }
}
