<?php

namespace App\Controller\api;

use App\Entity\Newsletter;
use App\Repository\NewsletterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/newsletter')]
class NewsletterController extends AbstractController
{
    #[Route('/', name: 'app_newsletter_index', methods: ['GET'])]
    public function getEmails(NewsletterRepository $newsletterRepository): Response
    {
        $emails = $newsletterRepository->findAll();
        return $this->json($emails);
    }

    #[Route('/new', name: 'app_newsletter_new', methods: ['POST'])]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            try {
                // Récupérer les données envoyées depuis Postman
                $data = json_decode($request->getContent(), true);

                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    return new JsonResponse(['error' => 'L\'adresse email fournie n\'est pas valide']);
                }

                $newsletter = new Newsletter();

                $newsletter->setEmail($data['email']);

                $entityManager->persist($newsletter);
                $entityManager->flush();

                return new JsonResponse(['message' => 'Email ajouté à la newsletter']);
            } catch (Exception $e) {
                return new JsonResponse(['error' => 'L\'email est déjà enregistré dans la newsletter']);
            }
        }

        return new JsonResponse(['message' => 'Méthode non autorisée. Veuillez utiliser une requête POST.'], Response::HTTP_METHOD_NOT_ALLOWED);
    }
}
