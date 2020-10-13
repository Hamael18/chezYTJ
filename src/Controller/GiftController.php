<?php

namespace App\Controller;

use App\Entity\Gift;
use App\Form\GiftType;
use App\Repository\GiftRepository;
use App\Service\UploadCloudinary;
use Cloudinary\Uploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GiftController extends AbstractController
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
            $this->uploadCloudinary->handleImageCloudinary($form, $gift);
            $this->manager->persist($gift);
            $this->manager->flush();
            $this->addFlash('success','Le cadeau a été créé');

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
            $this->uploadCloudinary->handleImageCloudinary($form, $gift);
            $this->manager->flush();
            $this->addFlash('success','Le cadeau a été édité.');

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
        if ($this->isCsrfTokenValid('gift_deletion_'.$gift->getId(), $request->request->get('csrf_token'))) {
            $this->manager->remove($gift);
            $this->manager->flush();
        }

        return $this->redirectToRoute('gifts');
    }
}
