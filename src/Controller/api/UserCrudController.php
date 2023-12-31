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

    #[Route('/findBy', name: 'app_user_findBy', methods: ['POST'])]
    public function findBy(UserRepository $userRepository, Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $data = json_decode($request->getContent(), true);

            $users = $userRepository->findOneBy(['email' => $data['email']]);
            return $this->json(
                $users,
                context: ['groups' => 'user:read']
            );
        }
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

    #[Route('/{id}/editAdmin', name: 'app_user_editAdmin', methods: ['GET', 'POST'])]
    public function editAdmin(Request $request, User $user, EntityManagerInterface $entityManager, AdressRepository $adressRepository): Response
    {
        if ($request->isMethod('POST')) {
            // Récupérer les données envoyées depuis Postman
            $data = json_decode($request->getContent(), true);
            if (
                !isset($data['pseudo']) ||
                !isset($data['email']) ||
                !isset($data['gender']) ||
                !isset($data['lastname']) ||
                !isset($data['firstname']) ||
                !isset($data['birthDate']) ||
                !isset($data['adress'])
            ) {
                if (!isset($data['pseudo'])) {
                    $missingKeys[] = 'pseudo';
                }
                if (!isset($data['email'])) {
                    $missingKeys[] = 'email';
                }
                if (!isset($data['gender'])) {
                    $missingKeys[] = 'gender';
                }
                if (!isset($data['lastname'])) {
                    $missingKeys[] = 'lastname';
                }
                if (!isset($data['firstname'])) {
                    $missingKeys[] = 'firstname';
                }
                if (!isset($data['birthDate'])) {
                    $missingKeys[] = 'birthDate';
                }
                if (!isset($data['adress'])) {
                    $missingKeys[] = 'adress';
                }

                return new JsonResponse(['message' => 'Données manquantes.', 'missing_keys' => $missingKeys], Response::HTTP_BAD_REQUEST);
            }

            $user->setPseudo($data['pseudo']);
            $user->setEmail($data['email']);
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
