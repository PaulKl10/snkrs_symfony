<?php

namespace App\Controller\api;

use App\Entity\Adress;
use App\Form\AdressType;
use App\Repository\AdressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/adress')]
class AdressCrudController extends AbstractController
{
    #[Route('/', name: 'app_adress_index', methods: ['GET'])]
    public function index(AdressRepository $adressRepository): Response
    {
        $adresses = $adressRepository->findAll();
        return $this->json($adresses, context: ['groups' => 'adress:read']);
    }

    #[Route('/new', name: 'app_adress_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, AdressRepository $adressRepository): Response
    {
        if ($request->isMethod('POST')) {
            // Récupérer les données envoyées depuis Postman
            $data = json_decode($request->getContent(), true);
            // Vérifier si l'utilisateur existe déjà par e-mail ou pseudo
            $existingAdress = $adressRepository->findOneBy(['street' => $data['street']]);

            if ($existingAdress) {
                return new JsonResponse(['message' => 'Cet adresse existe déjà.']);
            }

            $adress = new Adress();

            $adress->setStreet($data['street']);
            $adress->setCodepostal($data['code_postal']);
            $adress->setCity($data['city']);

            $entityManager->persist($adress);
            $entityManager->flush();

            return new JsonResponse(['message' => 'Nouvelle adresse enregistrée avec succès.', 'id' => $adress->getId()]);
        }

        return new JsonResponse(['message' => 'Méthode non autorisée. Veuillez utiliser une requête POST.'], Response::HTTP_METHOD_NOT_ALLOWED);
    }

    #[Route('/{id}', name: 'app_adress_show', methods: ['GET'])]
    public function show(Adress $adress): Response
    {
        return $this->json($adress, context: ['groups' => 'adress:read']);
    }

    #[Route('/{id}/edit', name: 'app_adress_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Adress $adress, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            // Récupérer les données envoyées depuis Postman
            $data = json_decode($request->getContent(), true);

            $adress->setStreet($data['street']);
            $adress->setCodepostal($data['code_postal']);
            $adress->setCity($data['city']);

            $entityManager->flush();

            return new JsonResponse(['message' => 'Adresse modifié avec succès.', 'id' => $adress->getId()]);
        }
    }

    #[Route('/{id}', name: 'app_adress_delete', methods: ['POST'])]
    public function delete(Request $request, Adress $adress, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($adress);
        $entityManager->flush();

        return $this->redirectToRoute('app_adress_index', [], Response::HTTP_SEE_OTHER);
    }
}
