<?php

namespace App\Controller\api;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/category')]
class CategoryCrudController extends AbstractController
{
    #[Route('/', name: 'app_category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        return $this->json($categories, context: ['groups' => 'category:read']);
    }

    #[Route('/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            // Récupérer les données envoyées depuis Postman
            $data = json_decode($request->getContent(), true);

            $category = new Category();

            $category->setName($data['name']);
            $category->setDescription($data['description']);

            $entityManager->persist($category);
            $entityManager->flush();

            return new JsonResponse(['message' => 'Nouvelle catégorie enregistrée avec succès.']);
        }

        return new JsonResponse(['message' => 'Méthode non autorisée. Veuillez utiliser une requête POST.'], Response::HTTP_METHOD_NOT_ALLOWED);
    }


    #[Route('/{id}', name: 'app_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->json($category, context: ['groups' => 'category:read']);
    }

    #[Route('/{id}/edit', name: 'app_category_edit', methods: ['POST'])]
    public function edit(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            // Récupérer les données envoyées depuis Postman
            $data = json_decode($request->getContent(), true);

            $category->setName($data['name']);
            $category->setDescription($data['description']);

            $entityManager->flush();

            return new JsonResponse(['message' => 'Catégorie modifié avec succès.']);
        }
    }

    #[Route('/{id}/delete', name: 'app_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {

        $entityManager->remove($category);
        $entityManager->flush();

        return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
