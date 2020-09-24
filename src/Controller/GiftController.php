<?php

namespace App\Controller;

use App\Entity\Gift;
use App\Form\GiftType;
use App\Repository\GiftRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GiftController extends AbstractController
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("/gifts/", name="gifts", methods={"GET"})
     */
    public function index(GiftRepository $giftRepository): Response
    {
        return $this->render('gifts/index.html.twig', [
            'gifts' => $giftRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/gifts/create", name="app_gifts_create", methods={"GET","POST"})
     */
    public function create(Request $request): Response
    {
        $gift = new Gift();
        $form = $this->createForm(GiftType::class, $gift);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($gift);
            $entityManager->flush();
            $this->addFlash('success','Le cadeau a été créé⋅e');

            return $this->redirectToRoute('gifts');
        }

        return $this->render('gifts/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/gifts/{id}", name="app_gifts_show", methods={"GET"})
     */
    public function show(Gift $gift): Response
    {
        return $this->render('gifts/show.html.twig', [
            'gift' => $gift,
        ]);
    }

    /**
     * @Route("/admin/gifts/{id}/edit", name="app_gifts_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Gift $gift): Response
    {
        $form = $this->createForm(GiftType::class, $gift);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('gifts');
        }

        return $this->render('gifts/edit.html.twig', [
            'gift' => $gift,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/gifts/{id}", name="app_gifts_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Gift $gift): Response
    {
        if ($this->isCsrfTokenValid('delete'.$gift->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($gift);
            $entityManager->flush();
        }

        return $this->redirectToRoute('gifts');
    }
}
