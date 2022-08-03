<?php

namespace App\Controller;

use App\Entity\Crud;
use App\Form\CrudType;
use App\Repository\CrudRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FileUploader;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/crud')]
class CrudController extends AbstractController
{
    #[Route('/', name: 'app_crud_index', methods: ['GET'])]
    public function index(CrudRepository $crudRepository): Response
    {
        return $this->render('crud/index.html.twig', [
            'cruds' => $crudRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_crud_new', methods: ['GET', 'POST'])]
    public function new(ManagerRegistry $doctrine ,Request $request, CrudRepository $crudRepository, FileUploader $fileUploader): Response
    {
        $crud = new Crud($crudRepository);
        $form = $this->createForm(CrudType::class, $crud);
        $form->handleRequest($request);
        $crud = $form->getData();
        // $crud->setFkStatus();
        // dd($crud);
        if ($form->isSubmitted() && $form->isValid()) {
            // dd($crud);
 
            $pictureFile = $form->get('picture')->getData();
            //picture is the name given to the input field
            
            if ($pictureFile) {
            $pictureFileName = $fileUploader->upload($pictureFile);
            $crud->setPicture($pictureFileName);
            }
            // $crud = $form->getData();
            // $em = $doctrine->getManager();
            // $em->persist($crud);
            // $em->flush();
            $crudRepository->add($crud, true);
            return $this->redirectToRoute('app_crud_index', [], Response::HTTP_SEE_OTHER);
            
        }

        return $this->renderForm('crud/new.html.twig', [
            'crud' => $crud,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_crud_show', methods: ['GET'])]
    public function show(Crud $crud): Response
    {
        return $this->render('crud/show.html.twig', [
            'crud' => $crud,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_crud_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Crud $crud, CrudRepository $crudRepository): Response
    {
        $form = $this->createForm(CrudType::class, $crud);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $crudRepository->add($crud, true);

            return $this->redirectToRoute('app_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('crud/edit.html.twig', [
            'crud' => $crud,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_crud_delete', methods: ['POST'])]
    public function delete(Request $request, Crud $crud, CrudRepository $crudRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$crud->getId(), $request->request->get('_token'))) {
            $crudRepository->remove($crud, true);
        }

        return $this->redirectToRoute('app_crud_index', [], Response::HTTP_SEE_OTHER);
    }
}
