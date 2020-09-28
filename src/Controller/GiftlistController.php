<?php

namespace App\Controller;

use App\Entity\Gift;
use App\Entity\User;
use App\Form\GiftListType;
use App\Repository\GiftRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GiftlistController extends AbstractController
{
    protected $userRepository;

    protected $giftRepository;

    private $manager;

    public function __construct(EntityManagerInterface $manager, UserRepository $userRepository, GiftRepository $giftRepository)
    {
        $this->manager = $manager;
        $this->userRepository = $userRepository;
        $this->giftRepository = $giftRepository;
    }

    /**
     * @Route("/giftlists", name="giftlists")
     */
    public function index()
    {
        $users = $this->userRepository->findAll();
        return $this->render('giftlist/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/giftlist/{firstName}", name="app_giftlists_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return$this->render('giftlist/show.html.twig', compact('user'));
    }

    /**
     * @Route("/admin/giftlist/{id<[0-9]+>}/add", name="app_giftlists_add", methods={"GET", "POST"})
     */
    public function add(Request $request, User $user): Response
    {
        $form = $this->createForm(GiftListType::class, $user, [
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();

            $this->addFlash('success',"Le cadeau a été ajouté à la liste de $user");

            return $this->redirectToRoute('giftlists');
        }

        return $this->render('giftlist/add.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/user/{user_id<[0-9]+>}/gift/{gift_id<[0-9]+>}", name="app_user_gift_remove", methods={"DELETE"})
     * @ParamConverter("user", class="App:User", options={"id": "user_id"})
     * @ParamConverter("gift", class="App:Gift", options={"id": "gift_id"})
     */
    public function remove(User $user, Gift $gift, Request $request): Response
    {
        dd($gift);
        if($this->isCsrfTokenValid('user_gift_deletion_' . $gift->getId(), $request->request->get("csrf_token") ))
        {
            $user->removeGift($gift);
            $this->manager->persist($gift);
            $this->manager->flush();
            $this->addFlash('info',"Le cadeau a été retriré de la liste de $user.");
        }

        return $this->redirectToRoute('app_giftlists_show', ['firstName' => $user->getFirstName()]);

    }
}
