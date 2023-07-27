<?php

namespace App\Controller\api;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class ApiLogController extends AbstractController
{
    public function __construct(private UserPasswordHasherInterface $passwordEncoder, private JWTTokenManagerInterface $JWTManager)
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
            }
            // Génére le token JWT
            $token = $this->generateTokenForUser($user, $this->JWTManager);

            // Répondre avec le token JWT
            return new JsonResponse(['status' => true, 'token' => $token], Response::HTTP_OK);
        }
        return new JsonResponse(['message' => 'Méthode non autorisée. Veuillez utiliser une requête POST.'], Response::HTTP_METHOD_NOT_ALLOWED);
    }

    #[Route('/api/logout', name: 'app_api_logout', methods: ['POST'])]
    public function logout()
    {
        return new JsonResponse(['message' => 'Déconnexion réussie']);
    }

    private function generateTokenForUser(User $user, JWTTokenManagerInterface $tokenManager): string
    {
        $payload = [
            'email' => $user->getUserIdentifier()
        ];

        // Génére le token JWT 
        return $tokenManager->create($user, $payload);
    }
}
