<?php

namespace App\Controller\admin;

use App\Entity\Adress;
use App\Form\AdressType;
use App\Repository\AdressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('admin/adress')]
class AdressController extends AbstractController
{
    #[Route('/', name: 'app_adress_index', methods: ['GET'])]
    public function index(AdressRepository $adressRepository): Response
    {
        $adresses = $adressRepository->findAll();
        $adressesData = [];

        // Itérer sur les catégories pour récupérer les informations nécessaires
        foreach ($adresses as $adress) {
            $adressesData[] = [
                'id' => $adress->getId(),
                'street' => $adress->getStreet(),
                'code_postal' => $adress->getCodepostal(),
                'city' => $adress->getCity()
            ];
        }
        return new JsonResponse($adressesData);
    }

    #[Route('/new', name: 'app_adress_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            // Récupérer les données envoyées depuis Postman
            $data = json_decode($request->getContent(), true);

            $adress = new Adress();

            $adress->setStreet($data['street']);
            $adress->setCodepostal($data['code_postal']);
            $adress->setCity($data['city']);

            $entityManager->persist($adress);
            $entityManager->flush();

            return new JsonResponse(['message' => 'Nouvelle adresse enregistrée avec succès.']);
        }

        return new JsonResponse(['message' => 'Méthode non autorisée. Veuillez utiliser une requête POST.'], Response::HTTP_METHOD_NOT_ALLOWED);
    }

    #[Route('/{id}', name: 'app_adress_show', methods: ['GET'])]
    public function show(Adress $adress): Response
    {
        $adressData[] = [
            'id' => $adress->getId(),
            'street' => $adress->getStreet(),
            'code_postal' => $adress->getCodepostal(),
            'city' => $adress->getCity()
        ];

        return new JsonResponse($adressData);
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

            return new JsonResponse(['message' => 'Adresse modifié avec succès.']);
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