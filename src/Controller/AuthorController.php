<?php
namespace App\Controller;

use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class AuthorController extends AbstractController{
    //l'accueil
    /**
     * @Route("/", name="accueil")
     */
    public function accueil(AuthorRepository $authorRepository, BookRepository $bookRepository){

        $books = $bookRepository ->findBy([], ['id'=>'DESC'],  3);
        $authors = $authorRepository ->findBy([], ['id'=>'DESC'],  3);

        return $this->render('accueil.html.twig', [
            'books' => $books,
            'authors' => $authors
        ]);
    }

//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

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

//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

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

//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

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

//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

    //je recherche les livres selon un mot que j'ai entré
    //on reserve les wildcart pour les valeurs simple (id, nom...)
    //ici on utilise plutot les request pour rechercher des chaines de caractères
    //avec les class BookRepository et Request on demande à symfony d'instancier notre
    // en les passant en parametre : c'est appelé convoyeur
    /**
     * @route("/books/search/resume", name="search_result")
     */
    public function SearchByResum(
        BookRepository $bookRepository,
        Request $request)
    {
        //utiliser la class request pour récup la valeur ds l'url qui est envoyée par le formulaire
        $word = $request->query->get('search');
        //initialiser la variable books
        $books=[];
        //voir si la variable est vide ou non
        if (!empty($word)){
            //si elle n'est pas vide, renvoyer le résultat fourni par la méthode BookFindByResum
            //présent dans le repository $bookRepository
            $books=$bookRepository->BookFindByResum($word);
        }
        //appeler le fichier twig avec le résulta de la recherche donné par la méthode
        return $this->render('search.html.twig', [
          'books'=>$books
        ]);

    }

//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

    /**
     * @route("/setter_book", name="setter_book")
     */
    public function setter_book_endur(EntityManagerInterface $entityManager){
        // je créé une nouvelle instance de l'entité Book
        $newBook = new book();
        // je lui donne les valeurs des colonnes avec les setters
        $newBook -> setTitle('test');
        $newBook -> setGenre('horreur');
        $newBook -> setNbPages(373);
        $newBook -> setResume('resume test');
        // j'utilise l'entityManager pour que Doctrine m'enregistre le livre créé avec la méthode persist()
        // puis je "valide" l'enregistrement avec la méthode flush()
        $entityManager-> persist($newBook);
        $entityManager-> flush();
    }

    /**
     * @Route("/newBook", name="newBook")
     */
    public function new_Book(AuthorRepository $authorRepository){
        return $this->render('admin_newBook.html.twig');
    }
    /**
     * @Route("/newAuthor", name="newAuthor")
     */
    public function newAuthor(AuthorRepository $authorRepository){
        return $this->render('admin_newAuthor.html.twig');
    }
    /**
     * @route("/update_book", name="update_book")
     */
    public function update_book(BookRepository $bookRepository, EntityManagerInterface $entityManager){
        //on récupère le livre selon l'id en placant dans la variable une entité
        $book = $bookRepository->find(1);
        //on change son titre
        $book->setTitle('Carrie');
        //on pousse l'update dans la BDD
        $entityManager->persist($book);
        $entityManager->flush();
    }
    /**
     * @route("/update_author/{id}", name="update_author")
     */
    public function update_author (BookRepository $bookRepository, EntityManagerInterface $entityManager){
        //on récupère le livre avec une entité que l'on place dans une variable
        $book = $bookRepository->find(18);
        //avec entity manager, on remove le livre
        $entityManager->remove($book);
        $entityManager->flush();
        //on envois un message de confirmation
        dump('livre supprimé');
        die;
    }

}

