<?php
namespace App\Controller;

use App\Entity\Book;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Repository\GenreRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;


class AuthorController extends AbstractController{
    //l'accueil
    /**
     * @Route("/", name="accueil")
     */
    public function accueil(AuthorRepository $authorRepository, BookRepository $bookRepository, GenreRepository $genreRepository){

        $books = $bookRepository ->findBy([], ['id'=>'DESC'],  3);
        $authors = $authorRepository ->findBy([], ['id'=>'DESC'],  3);
        $genres = $genreRepository->findBy([], ['id' => 'DESC']);

        return $this->render('accueil.html.twig', [
            'books' => $books,
            'authors' => $authors,
            'genres' => $genres
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

    //je recherche les livres selon leur genre
    /**
     * @Route("/books/{name}", name="booksGenre")
     */
    public function booksGenre(
        GenreRepository $genreRepository,
        BookRepository $bookRepository,
        $name
    ){
        //on récupère les livres selon le genre que l'on a entrée
        //ici on récupère un seul genre avec findOneBy
        $genre = $genreRepository ->findOneBy(['name'=> $name]);
        //ici on récupère plusieurs livres qui correspondent au genre récupéré
        $books = $bookRepository->findBy(['genre'=> $genre]);

        //on retourne les infos récupérées
        return $this->render('books_genre.html.twig', [
            'genre'=>$genre,
            'books'=>$books
        ]);
    }

    //je recherche les livres selon une liste des genres
    /**
     * @Route("/books/genrefind", name="allBooksGenre")
     */
    public function allBooksGenre(){
        return $this->render('books_genre.html.twig');
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
//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

    //route pour l'inscription
    /**
     * @route("/inscription", name="inscription")
     */
    public function inscription(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    )
    {
        //nouvelle instance
        $user = new User();
        //recupération du gabarit de formulaire
        $userForm = $this->createForm(UserType ::class, $user);
        //je prends les données de la requete et je les envois au formulaire
        $userForm->handleRequest($request);
        //si le formulaire a ete envoyé et que les données sont valides...
        if ($userForm->isSubmitted()&&$userForm->isValid()){
            //... alors je persist et flush le livre
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Merci de votre inscription');
            return $this->redirectToRoute('userAccueil');
        }
        return $this->render('inscription.html.twig', [
            'userForm'=>$userForm->createView()
        ]);
    }
}

