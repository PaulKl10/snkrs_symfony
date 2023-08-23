<?php

namespace App\Controller\api;

use App\Entity\Nft;
use App\Entity\PurchaseNft;
use App\Form\PurchaseNftType;
use App\Repository\NftRepository;
use App\Repository\PurchaseNftRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/purchaseNft')]
class PurchaseNftCrudController extends AbstractController
{
    #[Route('/', name: 'app_purchase_nft_index', methods: ['GET'])]
    public function index(PurchaseNftRepository $purchaseNftRepository): Response
    {
        $purchases = $purchaseNftRepository->findAll();
        $purchasesData = [];

        // Itérer sur les catégories pour récupérer les informations nécessaires
        foreach ($purchases as $purchase) {
            $purchasesData[] = [
                'id' => $purchase->getId(),
                'nft' => $purchase->getNft(),
                'user' => $purchase->getUser(),
                'date' => $purchase->getPurchaseDate(),
                'prix euro' => $purchase->getNftEurPrice(),
                'prix ETH' => $purchase->getNftEthPrice(),
            ];
        }
        return new JsonResponse($purchasesData);
    }

    #[Route('/new', name: 'app_purchase_nft_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, NftRepository $nftRepository, UserRepository $userRepository): Response
    {
        if ($request->isMethod('POST')) {

            $data = json_decode($request->getContent(), true);
            $purchase = new PurchaseNft();

            $purchase->setNft($nftRepository->find($data['nft']));
            $purchase->setUser($userRepository->find($data['user']));
            $purchase->setPurchaseDate(new DateTime());
            $purchase->setNftEurPrice($data['prix_eur']);
            $purchase->setNftEthPrice($data['prix_eth']);

            $entityManager->persist($purchase);
            $entityManager->flush();

            return new JsonResponse(['message' => 'Nouvelle achat enregistré avec succès.']);
        }

        return new JsonResponse(['message' => 'Méthode non autorisée. Veuillez utiliser une requête POST.'], Response::HTTP_METHOD_NOT_ALLOWED);
    }

    #[Route('/{id}', name: 'app_purchase_nft_show', methods: ['GET'])]
    public function show(PurchaseNft $purchaseNft): Response
    {
        return $this->render('purchase_nft/show.html.twig', [
            'purchase_nft' => $purchaseNft,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_purchase_nft_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PurchaseNft $purchaseNft, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PurchaseNftType::class, $purchaseNft);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_purchase_nft_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('purchase_nft/edit.html.twig', [
            'purchase_nft' => $purchaseNft,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_purchase_nft_delete', methods: ['POST'])]
    public function delete(Request $request, PurchaseNft $purchaseNft, EntityManagerInterface $entityManager): Response
    {
        // if ($this->isCsrfTokenValid('delete' . $purchaseNft->getId(), $request->request->get('_token'))) {
        $entityManager->remove($purchaseNft);
        $entityManager->flush();
        // }

        return $this->redirectToRoute('app_purchase_nft_index', [], Response::HTTP_SEE_OTHER);
    }
}
