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

#[Route('api/user')]
class UserCrudController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->json(
            $users,
            context: ['groups' => 'user:read']
        );
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, AdressRepository $adressRepository, UserPasswordHasherInterface $hasher): Response
    {

        if ($request->isMethod('POST')) {
            // Récupérer les données envoyées depuis Postman
            $data = json_decode($request->getContent(), true);

            $user = new User();

            $user->setPseudo($data['pseudo']);
            $user->setEmail($data['email']);
            $user->setPassword($hasher->hashPassword($user, $data['password']));
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

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->json($user, context: ['groups' => 'user:read']);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, AdressRepository $adressRepository, UserPasswordHasherInterface $hasher): Response
    {
        if ($request->isMethod('POST')) {
            // Récupérer les données envoyées depuis Postman
            $data = json_decode($request->getContent(), true);

            $user->setPseudo($data['pseudo']);
            $user->setEmail($data['email']);
            $user->setPassword($hasher->hashPassword($user, $data['password']));
            $user->setGender($data['gender']);
            $user->setLastname($data['lastname']);
            $user->setFirstname($data['firstname']);
            $user->setBirthdate(new DateTime($data['birthDate']));
            $user->setAdress($adressRepository->find($data['adress']));

            $entityManager->flush();

            return new JsonResponse(['message' => 'utilisateur modifié avec succès.']);
        }

        return new JsonResponse(['message' => 'Méthode non autorisée. Veuillez utiliser une requête POST.'], Response::HTTP_METHOD_NOT_ALLOWED);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['DELETE'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
