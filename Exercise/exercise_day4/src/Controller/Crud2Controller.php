<?php

namespace App\Controller;

use App\Entity\Crud;
use App\Form\CrudType;
use App\Repository\CrudRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/crud2')]
class Crud2Controller extends AbstractController
{
    #[Route('/', name: 'app_crud2_index', methods: ['GET'])]
    public function index(CrudRepository $crudRepository): Response
    {
        return $this->render('crud2/index.html.twig', [
            'cruds' => $crudRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_crud2_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CrudRepository $crudRepository): Response
    {
        $crud = new Crud();
        $form = $this->createForm(CrudType::class, $crud);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $crudRepository->add($crud, true);

            return $this->redirectToRoute('app_crud2_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('crud2/new.html.twig', [
            'crud' => $crud,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_crud2_show', methods: ['GET'])]
    public function show(Crud $crud): Response
    {
        return $this->render('crud2/show.html.twig', [
            'crud' => $crud,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_crud2_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Crud $crud, CrudRepository $crudRepository): Response
    {
        $form = $this->createForm(CrudType::class, $crud);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $crudRepository->add($crud, true);

            return $this->redirectToRoute('app_crud2_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('crud2/edit.html.twig', [
            'crud' => $crud,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_crud2_delete', methods: ['POST'])]
    public function delete(Request $request, Crud $crud, CrudRepository $crudRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$crud->getId(), $request->request->get('_token'))) {
            $crudRepository->remove($crud, true);
        }

        return $this->redirectToRoute('app_crud2_index', [], Response::HTTP_SEE_OTHER);
    }
}
