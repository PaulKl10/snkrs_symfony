<?php

namespace App\Controller\api;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ApiLogController extends AbstractController
{
    public function __construct(private UserPasswordHasherInterface $passwordEncoder)
    {
    }

    #[Route('/api/login', name: 'app_api_login', methods: ['POST'])]
    public function login(Request $request, UserRepository $userRepository): Response
    {
        if ($request->isMethod('POST')) {
            // Récupérez les données du formulaire soumises par le front-end
            $data = json_decode($request->getContent(), true);

            $email = $data['email'];
            $password = $data['password'];

            $user = $userRepository->findOneBy(['email' => $email]);

            // Vérifiez si l'utilisateur existe et si le mot de passe est correct, par exemple :
            if (!$user || !$this->passwordEncoder->isPasswordValid($user, $password)) {
                // L'utilisateur n'a pas été trouvé ou le mot de passe est incorrect
                return $this->json(['message' => 'Identifiants invalides'], Response::HTTP_UNAUTHORIZED);
            } else {
                return $this->json(['message' => 'Vous êtes bien connectés'], Response::HTTP_ACCEPTED);
            }
        }
        return new JsonResponse(['message' => 'Méthode non autorisée. Veuillez utiliser une requête POST.'], Response::HTTP_METHOD_NOT_ALLOWED);
    }
}
