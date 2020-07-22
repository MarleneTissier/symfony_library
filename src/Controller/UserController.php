<?php

    namespace App\Controller;

    use App\Repository\AuthorRepository;
    use App\Repository\BookRepository;
    use App\Repository\GenreRepository;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\HttpFoundation\Request;

    class UserController extends AbstractController{

        //l'accueil
        /**
         * @Route("/", name="userAccueil")
         */
        public function accueil(AuthorRepository $authorRepository, BookRepository $bookRepository, GenreRepository $genreRepository){

            $books = $bookRepository ->findBy([], ['id'=>'DESC'],  3);
            $authors = $authorRepository ->findBy([], ['id'=>'DESC'],  3);
            $genres = $genreRepository->findBy([], ['id' => 'DESC']);

            return $this->render('user_accueil.html.twig', [
                'books' => $books,
                'authors' => $authors,
                'genres' => $genres
            ]);
        }


    }