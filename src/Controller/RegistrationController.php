<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, SerializerInterface $serializer, UserRepository $repo): Response
    {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        if (strlen($user->getPassword()) < 6) {
            return $this->json("too short, need 6+ chars", 401);
        }

        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $user->getPassword()
            )
        );
        $exists = $repo->findOneBy(["username" => $user->getUsername()]);
        if (!$exists) {
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->json($user, 200, [], ['groups' => 'user:read']);
        } else {
            return $this->json("email already used", 401);
        }
    }
}
