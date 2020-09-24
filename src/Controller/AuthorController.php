<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
* @Route("/admin")
*/
class AuthorController extends AbstractController
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("/authors", name="authors")
     */
    public function index(AuthorRepository $authorRepository)
    {
        $authors = $authorRepository->findBy([], ['lastName' => 'ASC' ]);
        return $this->render('authors/index.html.twig', compact('authors'));
    }

    /**
     * @Route("/author/{id<[0-9]+>}", name="app_authors_show", methods={"GET"})
     */
    public function show(Author $author): Response
    {
        return$this->render('authors/show.html.twig', compact('author'));

    }

    /**
     * @Route("/author/create", name="app_authors_create", methods={"GET", "POST"})
     */
    public function create(Request $request): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($author);
            $this->manager->flush();

            $this->addFlash('success','L\'auteur⋅trice a été créé⋅e');

            return $this->redirectToRoute('authors');
        }

        return $this->render('authors/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/author/{id<[0-9]+>}/edit", name="app_authors_edit", methods={"GET", "PUT"})
     */
    public function edit(Author $author, Request $request): Response
    {
        $form = $this->createForm(AuthorType::class, $author, [
            'method' => 'PUT'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();

            $this->addFlash('success','L\'auteur⋅trice a été édité⋅e');

            return $this->redirectToRoute('authors');
        }

        return $this->render('authors/edit.html.twig', [
            'form' => $form->createView(),
            'author' => $author
        ]);
    }

    /**
     * @Route("/author/{id<[0-9]+>}", name="app_authors_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Author $author): Response
    {
        if($this->isCsrfTokenValid('author_deletion_' . $author->getId(), $request->request->get("csrf_token") ))
        {
            $this->manager->remove($author);
            $this->manager->flush();
            $this->addFlash('info','L\'auteur⋅trice a été supprimé⋅e');

        }

        return $this->redirectToRoute('authors');

    }

    /**
     * @Route("/author/{author_id<[0-9]+>}/book/{book_id<[0-9]+>}", name="app_authors_book_remove", methods={"DELETE"})
     * @ParamConverter("author", class="App:Author", options={"id": "author_id"})
     * @ParamConverter("book", class="App:Book", options={"id": "book_id"})
     */
    public function removeAuthorBook(Author $author, Book $book, Request $request): Response
    {
        if($this->isCsrfTokenValid('author_book_deletion_' . $author->getId(), $request->request->get("csrf_token") ))
        {
            $author->removeBook($book);
            $this->manager->persist($book);
            $this->manager->flush();
            $this->addFlash('info','Le livre a été retriré de l\'auteur⋅trice. ');
        }

        return $this->redirectToRoute('app_authors_show', ['id' => $author->getId()]);

    }
}
