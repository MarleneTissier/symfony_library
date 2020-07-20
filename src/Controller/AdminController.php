<?php

    namespace App\Controller;

    use App\Entity\Book;
    use App\Form\BookType;
    use App\Entity\Author;
    use App\Form\AuthorType;
    use App\Repository\AuthorRepository;
    use App\Repository\BookRepository;
    use App\Repository\GenreRepository;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Validator\Constraints as Assert;
    use function Symfony\Component\String\b;

    class AdminController extends AbstractController
{

    //pour aller sur la page accueil ;
    /**
     * @Route("/admin", name="admin_accueil")
     */
    public function admin_accueil(AuthorRepository $authorRepository, BookRepository $bookRepository)
    {

        $books = $bookRepository->findBy([], ['id' => 'DESC'], 3);
        $authors = $authorRepository->findBy([], ['id' => 'DESC'], 3);

        return $this->render('admin/admin_accueil.html.twig', [
            'books' => $books,
            'authors' => $authors
        ]);
    }

    //pour aller sur la page de validation d'opération ;

    /**
     * @Route("/admin_validation", name="admin_validation")
     */
    public function admin_validation()
    {
        return $this->render('admin/admin_validation.html.twig');
    }

    //pour aller sur la page des auteurs ;

    /**
     * @Route("/admin_authors", name="admin_authors")
     */
    public function admin_authors(AuthorRepository $authorRepository)
    {
        $authors = $authorRepository->findAll();
        return $this->render('admin/admin_authors.html.twig', [
            'authors' => $authors
        ]);
    }

    //pour aller sur la page d un auteur ;

    /**
     * @Route("/admin_author/{id}", name="admin_author")
     */
    public function admin_author(AuthorRepository $authorRepository, $id)
    {
        $authors = $authorRepository->find($id);
        return $this->render('admin/admin_author.html.twig', [
            'author' => $authors
        ]);
    }

    //pour aller sur la page des livres ;

    /**
     * @Route("/admin_books", name="admin_books")
     */
    public function admin_books(BookRepository $bookRepository)
    {
        $books = $bookRepository->findAll();
        return $this->render('admin/admin_books.html.twig', [
            'books' => $books
        ]);
    }

    //pour aller sur la page d un livre ;

    /**
     * @Route("/admin_book/{id}", name="admin_book")
     */
    public function admin_book(BookRepository $bookRepository, $id)
    {
        $book = $bookRepository->find($id);
        return $this->render('admin/admin_book.html.twig', [
            'book' => $book
        ]);
    }


    //pour aller sur la page de recherche ;

    /**
     * @route("/admin_books/admin_search/resume", name="admin_search_result")
     */
    public function admin_search_result(
        BookRepository $bookRepository,
        Request $request)
    {
        $word = $request->query->get('search');
        $books = [];
        if (!empty($word)) {
            $books = $bookRepository->BookFindByResum($word);
        }
        return $this->render('admin/admin_search.html.twig', [
            'books' => $books
        ]);
    }


    //pour aller sur la page de suppression de livre ;

    /**
     * @route("/delete_admin_books/{id}", name="delete_admin_books")
     */
    public function delete_admin_books(
        BookRepository $bookRepository,
        EntityManagerInterface $entityManager,
        $id
    )
    {
        $book = $bookRepository->find($id);
        $entityManager->remove($book);
        $entityManager->flush();
        $this->addFlash('success', 'Votre livre a été supprimé !');
        return $this->redirectToRoute('admin_books');
    }

    //pour aller sur la page de suppression d un auteur ;

    /**
     * @route("/delete_admin_author/{id}", name="delete_admin_author")
     */
    public function delete_admin_author(
        AuthorRepository $authorRepository,
        EntityManagerInterface $entityManager,
        $id)
    {
        $autor = $authorRepository->find($id);
        $entityManager->remove($autor);
        $entityManager->flush();
        $this->addFlash('success', 'Votre auteur a été supprimé !');
        return $this->redirectToRoute('admin_authors');
    }

    /**
     * @route("/formulaire_book", name="formulaire_book")
     */
    public function formulaire_book(
        Request $request,
        EntityManagerInterface $entityManager
    )
    {
        //nouvelle instance
        $book = new Book();
        //recupération du gabarit de formulaire
        //créé avec la commande make:form que je stock dans une variable
        $bookForm=$this->createForm(BookType::class, $book);
        //je prends les données de la requete et je les envois au formulaire
        $bookForm->handleRequest($request);
        //si le formulaire a ete envoyé et que les données sont valides...
        if ($bookForm->isSubmitted()&&$bookForm->isValid()){
            //... alors je persist et flush le livre
            $entityManager->persist($book);
            $entityManager->flush();
            $this->addFlash('success', 'Votre livre a été créé !');
            return $this->redirectToRoute('admin_books');
        }
        //je renvois le fichier twig
        return $this->render('admin/formulaire_book.html.twig', [
            'bookForm'=>$bookForm->createView()
        ]);
    }

    /**
     * @Route("/formulaire_author", name="formulaire_author")
     */
    public function formulaire_author(
        Request $request,
        EntityManagerInterface $entityManager
    )
    {
        $author = new Author();
        $authorForm=$this->createForm(AuthorType::class, $author);
        $authorForm->handleRequest($request);
        if ($authorForm->isSubmitted()&&$authorForm->isValid()){
            $entityManager->persist($author);
            $entityManager->flush();
            $this->addFlash('success', 'Votre auteur a été ajouté !');
            return $this->redirectToRoute('admin_authors');
        }
        return $this->render('admin/formulaire_author.html.twig', [
            'authorForm'=>$authorForm->createView()
        ]);
    }

    /**
     * @route("/update_admin_book/{id}", name="update_admin_book")
     */
    public function update_admin_book(
        Request $request,
        BookRepository $bookRepository,
        EntityManagerInterface $entityManager,
        $id
    ){
        //une nouvelle instance
        //dans laquelle on attribut le livre trouvé selon l'id
        $book = $bookRepository->find($id);
        //recupération du gabarit de formulaire
        //créé avec la commande make:form que je stock dans une variable
        $bookFormUpdate=$this->createForm(BookType::class, $book);
        //je prends les données de la requete et je les envois au formulaire
        $bookFormUpdate->handleRequest($request);
        //si le formulaire a ete envoyé et que les données sont valides...
        if ($bookFormUpdate->isSubmitted() && $bookFormUpdate->isValid()){
            //... alors je persist et flush le livre
            $entityManager->persist($book);
            $entityManager->flush();
            $this->addFlash('success', 'Votre livre a été mis à jour !');
            return $this->redirectToRoute('admin_books');
        }
        //je renvois le fichier twig
        return $this->render( 'admin/update_admin_book.html.twig', [
            'blocFormUpdate'=>$bookFormUpdate->createView()
        ]);
    }

    /**
     * @route("/update_admin_author/{id}", name="update_admin_author")
     */
    public function update_admin_author(
        Request $request,
        EntityManagerInterface $entityManager,
        AuthorRepository $authorRepository,
        $id
    ){
        $author = $authorRepository->find($id);
        $authorFormUpdate=$this->createForm(AuthorType::class, $author);
        $authorFormUpdate->handleRequest($request);
        if ($authorFormUpdate->isSubmitted() && $authorFormUpdate->isValid()){
            $entityManager->persist($author);
            $entityManager->flush();
            $this->addFlash('success', 'Votre auteur a été mis à jour !');
            return $this->redirectToRoute('admin_authors');
        }
        return $this->render( 'admin/update_admin_author.html.twig', [
            'blocFormUpdate'=>$authorFormUpdate->createView()
        ]);
    }

    /**
     * @Route("/admin/book/insertwithgenre", name="InsertBookGenre")
     */
    public function InsertBookGenre(
        GenreRepository $genreRepository,
        EntityManagerInterface $entityManager
    ){

        $genre = $genreRepository->find(2);

        $book = new Book();

        $book->setTitle('blabla tome 3');
        $book->setNbPages(256);
        $book->setResume('bla bla bla');
        $book->setGenre($genre);

        $entityManager->persist($book);
        $entityManager->flush();

        return new Response('livre enregistré');
    }

}

