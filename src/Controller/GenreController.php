<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/genre')]
class GenreController extends AbstractController
{
    #[Route('s', methods: ['GET'])]
    public function index(GenreRepository $genreRepository): Response
    {
        return $this->json($genreRepository->findAll(), Response::HTTP_OK, [], ['groups' => 'genre']);
    }

    #[Route('/add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer): Response
    {
        $genre = $serializer->deserialize($request->getContent(), Genre::class, 'json');
        $genre->setAuthor($this->getUser());
        $manager->persist($genre);
        $manager->flush();
        return $this->json($genre, Response::HTTP_CREATED, [], ['groups' => 'genre']);
    }

    #[Route('/{id}/edit', methods: ['PUT'])]
    public function edit(Genre $genre, Request $request, EntityManagerInterface $manager, SerializerInterface $serializer): Response
    {
        $editedGenre = $serializer->deserialize($request->getContent(), Genre::class, 'json', ['object_to_populate' => $genre]);
        if ($this->getUser() !== $genre->getAuthor()) {
            return $this->json("you can't edit this genre, it's not yours", Response::HTTP_FORBIDDEN);
        }
        $editedGenre->setAuthor($this->getUser());
        $manager->flush();
        return $this->json("Genre edited", Response::HTTP_OK);
    }

    #[Route('/{id}/delete', methods: ['DELETE'])]
    public function delete(Genre $genre, EntityManagerInterface $manager): Response
    {
        if ($this->getUser() !== $genre->getAuthor()) {
            return $this->json("you can't delete this genre, it's not yours", Response::HTTP_FORBIDDEN);
        }
        $manager->remove($genre);
        $manager->flush();
        return $this->json("Genre deleted", Response::HTTP_NO_CONTENT);
    }
}
