<?php

namespace App\Controller;

use App\Entity\NftPrice;
use App\Form\NftPriceType;
use App\Repository\NftPriceRepository;
use App\Repository\NftRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/nftPrice')]
class NftPriceController extends AbstractController
{
    #[Route('/', name: 'app_nft_price_index', methods: ['GET'])]
    public function index(NftPriceRepository $nftPriceRepository): Response
    {
        $nftsPrice = $nftPriceRepository->findAll();
        $nftPriceData = [];

        // Itérer sur les catégories pour récupérer les informations nécessaires
        foreach ($nftsPrice as $nftPrice) {
            $nftPriceData[] = [
                'id' => $nftPrice->getId(),
                'price_date' => $nftPrice->getPriceDate(),
                'price_ETH' => $nftPrice->getPriceEthValue(),
                'Nft_id' => $nftPrice->getNft()
            ];
        }
        return new JsonResponse($nftPriceData);
    }

    #[Route('/new', name: 'app_nft_price_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, NftRepository $nftRepository): Response
    {
        if ($request->isMethod('POST')) {
            // Récupérer les données envoyées depuis Postman
            $data = json_decode($request->getContent(), true);

            $nftPrice = new nftPrice();

            $nftPrice->setPriceDate(new DateTime());
            $nftPrice->setPriceEthValue($data['price_ETH']);
            $nftPrice->setNft($nftRepository->find($data['nft']));

            $entityManager->persist($nftPrice);
            $entityManager->flush();

            return new JsonResponse(['message' => 'Prix du NFT enregistrée avec succès.']);
        }

        return new JsonResponse(['message' => 'Méthode non autorisée. Veuillez utiliser une requête POST.'], Response::HTTP_METHOD_NOT_ALLOWED);
    }

    #[Route('/{id}', name: 'app_nft_price_show', methods: ['GET'])]
    public function show(NftPrice $nftPrice): Response
    {
        $nftPriceData[] = [
            'id' => $nftPrice->getId(),
            'price_date' => $nftPrice->getPriceDate(),
            'price_ETH' => $nftPrice->getPriceEthValue(),
            'Nft_id' => $nftPrice->getNft()
        ];
        return new JsonResponse($nftPriceData);
    }


    #[Route('/{id}/edit', name: 'app_nft_price_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, NftPrice $nftPrice, EntityManagerInterface $entityManager, nftRepository $nftRepository): Response
    {
        if ($request->isMethod('POST')) {
            // Récupérer les données envoyées depuis Postman
            $data = json_decode($request->getContent(), true);

            $nftPrice->setPriceDate(new DateTime());
            $nftPrice->setPriceEthValue($data['price_ETH']);
            $nftPrice->setNft($nftRepository->find($data['nft']));

            $entityManager->flush();

            return new JsonResponse(['message' => 'Prix du NFT modifié avec succès.']);
        }

        return new JsonResponse(['message' => 'Méthode non autorisée. Veuillez utiliser une requête POST.'], Response::HTTP_METHOD_NOT_ALLOWED);
    }

    #[Route('/{id}', name: 'app_nft_price_delete', methods: ['DELETE'])]
    public function delete(Request $request, NftPrice $nftPrice, EntityManagerInterface $entityManager): Response
    {

        $entityManager->remove($nftPrice);
        $entityManager->flush();

        return $this->redirectToRoute('app_nft_price_index', [], Response::HTTP_SEE_OTHER);
    }
}
