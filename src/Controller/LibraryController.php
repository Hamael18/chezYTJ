<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\User;
use App\Form\LibraryType;
use App\Repository\BookRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LibraryController extends AbstractController
{
    protected $userRepository;

    protected $bookRepository;

    private $manager;

    public function __construct(EntityManagerInterface $manager, UserRepository $userRepository, BookRepository $bookRepository)
    {
        $this->manager = $manager;
        $this->userRepository = $userRepository;
        $this->bookRepository = $bookRepository;
    }

    /**
     * @Route("/libraries", name="libraries")
     */
    public function index()
    {
        $users = $this->userRepository->findAll();

        return $this->render('libraries/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/library/{firstName}", name="app_libraries_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return$this->render('libraries/show.html.twig', compact('user'));
    }

    /**
     * @Route("/admin/library/{id<[0-9]+>}/add", name="app_libraries_add", methods={"GET", "POST"})
     */
    public function add(Request $request, User $user): Response
    {
        $form = $this->createForm(LibraryType::class, $user, [
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();

            $this->addFlash('success',"Le livre a été ajouté à la bibliothèque de $user");

            return $this->redirectToRoute('libraries');
        }

        return $this->render('libraries/add.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/user/{user_id<[0-9]+>}/book/{book_id<[0-9]+>}", name="app_user_book_remove", methods={"DELETE"})
     * @ParamConverter ("user", class="App:User", options={"id": "user_id"})
     * @ParamConverter("book", class="App:Book", options={"id": "book_id"})
     */
    public function remove(User $user, Book $book, Request $request): Response
    {
        if($this->isCsrfTokenValid('user_book_deletion_' . $book->getId(), $request->request->get("csrf_token") ))
        {
            $user->removeLibrary($book);
            $this->manager->persist($book);
            $this->manager->flush();
            $this->addFlash('info',"Le livre a été retriré de la bibliothèque de $user.");
        }

        return $this->redirectToRoute('app_libraries_show', ['firstName' => $user->getFirstName()]);

    }
}
