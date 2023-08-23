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

            // Vérifier les champs requis
            if (
                !isset($data['pseudo']) ||
                !isset($data['email']) ||
                !isset($data['password']) ||
                !isset($data['gender']) ||
                !isset($data['lastname']) ||
                !isset($data['firstname']) ||
                !isset($data['birthDate']) ||
                !isset($data['adress'])
            ) {
                return new JsonResponse(['message' => 'Données manquantes.'], Response::HTTP_BAD_REQUEST);
            }

            $user = new User();

            $user->setPseudo($data['pseudo']);
            $user->setEmail($data['email']);
            $user->setPassword($this->passwordEncoder->hashPassword($user, $data['password']));
            $user->setGender($data['gender']);
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
}
