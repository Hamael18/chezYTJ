<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Service\UploadCloudinary;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    private $manager;
    /**
     * @var UploadCloudinary
     */
    private $uploadCloudinary;

    public function __construct(EntityManagerInterface $manager, UploadCloudinary $uploadCloudinary)
    {
        $this->manager = $manager;
        $this->uploadCloudinary = $uploadCloudinary;
    }

    /**
     * @Route("/books", name="books")
     */
    public function index(BookRepository $bookRepository)
    {
        $books = $bookRepository->findBy([], ['title' => 'ASC' ]);
        return $this->render('books/index.html.twig', compact('books'));
    }
    /**
     * @Route("/book/{id<[0-9]+>}", name="app_books_show", methods={"GET"})
     */
    public function show(Book $book): Response
    {
        return$this->render('books/show.html.twig', compact('book'));

    }

    /**
     * @Route("/admin/book/create", name="app_books_create", methods={"GET", "POST"})
     */
    public function create(Request $request): Response
    {

        $book = new Book ();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $book->setImageUrl($this->uploadCloudinary->uploadToCloundinary($form));
            $this->manager->persist($book);
            $this->manager->flush();
            $this->addFlash('success','Le livre a été créé.');

            return $this->redirectToRoute('books');
        }

        return $this->render('books/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/book/{id<[0-9]+>}/edit", name="app_books_edit", methods={"GET", "PUT"})
     */
    public function edit(Book $book, Request $request): Response
    {
        $form = $this->createForm(BookType::class, $book, [
            'method' => 'PUT'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('imageFile')->getData()) {
                $book->setImageUrl($this->uploadCloudinary->uploadToCloundinary($form));
            }
            $this->manager->flush();
            $this->addFlash('success','Le livre a été édité.');

            return $this->redirectToRoute('books');
        }

        return $this->render('books/edit.html.twig', [
            'form' => $form->createView(),
            'book' => $book
        ]);
    }

    /**
     * @Route("/admin/book/{id<[0-9]+>}", name="app_books_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Book $book): Response
    {
        if($this->isCsrfTokenValid('book_deletion_' . $book->getId(), $request->request->get("csrf_token") ))
        {
            $this->manager->remove($book);
            $this->manager->flush();
            $this->addFlash('info','Le livre a été supprimé.');

        }

        return $this->redirectToRoute('books');

    }
}
