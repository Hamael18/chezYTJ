<?php

namespace App\Controller;

use App\Entity\Collection;
use App\Form\CollectionType;
use App\Repository\CollectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/collection")
 */
class CollectionController extends AbstractController
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("/", name="collections", methods={"GET"})
     */
    public function index(CollectionRepository $collectionRepository): Response
    {
        return $this->render('collection/index.html.twig', [
            'collections' => $collectionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/create", name="app_collection_create", methods={"GET","POST"})
     */
    public function create(Request $request): Response
    {
        $collection = new Collection();
        $form = $this->createForm(CollectionType::class, $collection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($collection);
            $this->manager->flush();

            $this->addFlash('success','La collection a été créée');


            return $this->redirectToRoute('collections');
        }

        return $this->render('collection/create.html.twig', [
            'collection' => $collection,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id<[0-9]+>}", name="app_collections_show", methods={"GET"})
     */
    public function show(Collection $collection): Response
    {
        return $this->render('collection/show.html.twig', [
            'collection' => $collection,
        ]);
    }

    /**
     * @Route("/{id<[0-9]+>}/edit", name="app_collections_edit", methods={"GET","PUT"})
     */
    public function edit(Request $request, Collection $collection): Response
    {
        $form = $this->createForm(CollectionType::class, $collection, [
            'method' => 'PUT'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();

            $this->addFlash('success','La collection a été édité');

            return $this->redirectToRoute('collections');
        }

        return $this->render('collection/edit.html.twig', [
            'collection' => $collection,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id<[0-9]+>}", name="app_collections_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Collection $collection): Response
    {
        if ($this->isCsrfTokenValid('collection_deletion_'.$collection->getId(), $request->request->get('_token'))) {
            $this->manager->remove($collection);
            $this->manager->flush();
        }

        return $this->redirectToRoute('collections');
    }
}
