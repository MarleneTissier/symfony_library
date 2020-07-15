<?php
namespace App\Controller;

use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController{
    //l'accueil
    /**
     * @Route("/", name="accueil")
     */
    public function accueil(){
        return $this->render('accueil.html.twig');
    }

    //la on va appeler la méthode qui consistera à envoyer le contenu de la table authors à la page
    //concernée en faisant appelle à la BDD
    /**
     * @Route("/authors", name="authors_list")
     */
    public function AuthorList(AuthorRepository $authorRepository){
        $authors = $authorRepository->findAll();
        return $this->render('authors.html.twig', [
            'authors' => $authors
        ]);
    }

    //On va faire pareil, sauf que cette fois, on va rechercher un seul auteur
    //grace à son id
    /**
     * @Route("/author/{id}", name="author_show")
     */
    public function AuthorShow(AuthorRepository $authorRepository, $id){

        $author = $authorRepository->find($id);
        return $this->render('author.html.twig', [
            'author' => $author
        ]);
    }

    //on créé la liste de livre qui doit apparaitre
    /**
     * @Route("/books", name="books_list")
     */
    public function BookList(BookRepository $bookRepository){
        $books = $bookRepository->findAll();
        return $this->render('books.html.twig', [
            'books' => $books
        ]);
    }
    //on créé le visuel des livres un à un
    /**
     * @Route("/book/{id}", name="book_show")
     */
    public function BookShow(BookRepository $bookRepository, $id){
        $book = $bookRepository->find($id);
        return $this->render('book.html.twig', [
            'book'=>$book
        ]);
    }

    //je recherche les livres selon le genre horreur
    /**
     * @Route("/books/{genre}", name="booksGenre")
     */
    public function booksGenre(BookRepository $bookRepository, $genre){
        $booksGenre = $bookRepository->findBy(['genre'=> $genre]);
        return $this->render('books_genre.html.twig', [
            'genre'=>$genre,
            'books'=>$booksGenre
        ]);
    }

    //je recherche les livres selon un mot que j'ai entré
    /**
     * @route("/books/search/resume", name="book_search_result")
     */
    public function SearchBookByResum(BookRepository $bookRepository)
    {
        $bookRepository->BookFindByResum();
    }
}
