<?php

namespace App\Controller\api;

use App\Entity\User;
use App\Repository\AdressRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
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

    #[Route('/api/register', name: 'app_api_register', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager, AdressRepository $adressRepository, UserRepository $userRepository)
    {
        if ($request->isMethod('POST')) {
            // Récupérer les données envoyées depuis Postman
            $data = json_decode($request->getContent(), true);

            // Vérifier si l'utilisateur existe déjà par e-mail ou pseudo
            $existingUser = $userRepository->findOneBy(['email' => $data['email']]);

            if ($existingUser) {
                return new JsonResponse(['message' => 'Cet utilisateur existe déjà.'], Response::HTTP_CONFLICT);
            }

            $user = new User();

            $user->setPseudo($data['pseudo']);
            $user->setEmail($data['email']);
            $user->setPassword($this->passwordEncoder->hashPassword($user, $data['password']));
            $user->setGender($data['gender']);
            $user->setRoles(['ROLE_USER']);
            $user->setLastname($data['lastname']);
            $user->setFirstname($data['firstname']);
            $user->setBirthdate(new DateTime($data['birthDate']));
            $user->setAdress($adressRepository->find($data['adress']));

            $entityManager->persist($user);
            $entityManager->flush();

            return new JsonResponse(['message' => 'Nouvelle utilisateur enregistré avec succès.']);
        }

        return new JsonResponse(['message' => 'Méthode non autorisée. Veuillez utiliser une requête POST.'], Response::HTTP_METHOD_NOT_ALLOWED);
    }

    private function generateTokenForUser(User $user, JWTTokenManagerInterface $tokenManager): string
    {
        $payload = [
            'email' => $user->getUserIdentifier(),
            'id' => $user->getId(),
        ];

        // Génére le token JWT 
        return $tokenManager->create($user, $payload);
    }
}
