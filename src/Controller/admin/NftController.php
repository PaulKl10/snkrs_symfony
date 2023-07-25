<?php

namespace App\Controller\admin;

use App\Entity\Nft;
use App\Form\NftType;
use App\Repository\CategoryRepository;
use App\Repository\NftRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('admin/nft')]
class NftController extends AbstractController
{
    #[Route('/', name: 'app_nft_index', methods: ['GET'])]
    public function index(NftRepository $nftRepository): Response
    {
        $nfts = $nftRepository->findAll();
        $nftData = [];

        // Itérer sur les catégories pour récupérer les informations nécessaires
        foreach ($nfts as $nft) {
            $nftData[] = [
                'id' => $nft->getId(),
                'name' => $nft->getName(),
                'description' => $nft->getDescription(),
                'stock' => $nft->getStock(),
                'launch_date' => $nft->getLaunchDate(),
                'category' => $nft->getCategory()->getName()
                // Ajoutez d'autres propriétés si nécessaire
            ];
        }
        return new JsonResponse($nftData);
    }

    #[Route('/new', name: 'app_nft_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository): Response
    {
        if ($request->isMethod('POST')) {
            // Récupérer les données envoyées depuis Postman
            $data = json_decode($request->getContent(), true);

            $nft = new Nft();

            $nft->setName($data['name']);
            $nft->setCategory($categoryRepository->find($data['category']));
            $nft->setDescription($data['description']);
            $nft->setImg($data['img']);
            $nft->setStock($data['stock']);
            $nft->setLaunchDate(new DateTime());

            $entityManager->persist($nft);
            $entityManager->flush();

            return new JsonResponse(['message' => 'Nouvelle nft enregistrée avec succès.']);
        }

        return new JsonResponse(['message' => 'Méthode non autorisée. Veuillez utiliser une requête POST.'], Response::HTTP_METHOD_NOT_ALLOWED);
    }

    #[Route('/{id}', name: 'app_nft_show', methods: ['GET'])]
    public function show(Nft $nft): Response
    {
        $nftData[] = [
            'id' => $nft->getId(),
            'name' => $nft->getName(),
            'description' => $nft->getDescription(),
            'stock' => $nft->getStock(),
            'launch_date' => $nft->getLaunchDate(),
            'category' => $nft->getCategory()->getName()
            // Ajoutez d'autres propriétés si nécessaire
        ];
        return new JsonResponse($nftData);
    }

    #[Route('/{id}/edit', name: 'app_nft_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Nft $nft, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository): Response
    {
        if ($request->isMethod('POST')) {
            // Récupérer les données envoyées depuis Postman
            $data = json_decode($request->getContent(), true);

            $nft->setName($data['name']);
            $nft->setCategory($categoryRepository->find($data['category']));
            $nft->setDescription($data['description']);
            $nft->setImg($data['img']);
            $nft->setStock($data['stock']);
            $nft->setLaunchDate(new DateTime());

            $entityManager->flush();

            return new JsonResponse(['message' => 'nft modifié avec succès.']);
        }

        return new JsonResponse(['message' => 'Méthode non autorisée. Veuillez utiliser une requête POST.'], Response::HTTP_METHOD_NOT_ALLOWED);
    }

    #[Route('/{id}', name: 'app_nft_delete', methods: ['POST'])]
    public function delete(Request $request, Nft $nft, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($nft);
        $entityManager->flush();

        return $this->redirectToRoute('app_nft_index', [], Response::HTTP_SEE_OTHER);
    }
}
