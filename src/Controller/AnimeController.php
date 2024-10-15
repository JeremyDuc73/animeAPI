<?php

namespace App\Controller;

use App\Entity\Anime;
use App\Entity\Genre;
use App\Entity\Studio;
use App\Repository\AnimeRepository;
use App\Repository\GenreRepository;
use App\Repository\StudioRepository;
use App\Service\StatusCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/anime')]
class AnimeController extends AbstractController
{
    #[Route('s', methods: ['GET'])]
    public function index(AnimeRepository $animeRepository): Response
    {
        return $this->json($animeRepository->findAll(), Response::HTTP_OK, [], ['groups' => 'anime']);
    }

    #[Route('/add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer, GenreRepository $genreRepository, StudioRepository $studioRepository): Response
    {
        $anime = $serializer->deserialize($request->getContent(), Anime::class, 'json', ['groups' => 'anime-create']);
        $data = json_decode($request->getContent(), true);
        $anime->setEntryAuthor($this->getUser());

        if (isset($data['genres'])) {
            foreach ($data['genres'] as $genreName) {
                $genre = $genreRepository->findOneBy(['name' => $genreName]);

                if (!$genre) {
                    return $this->json("The genre '$genreName' does not exist", Response::HTTP_NOT_FOUND);
                }

                $anime->addGenre($genre);
            }
        }
        if (isset($data['studio'])) {
            $studio = $studioRepository->findOneBy(['name' => $data['studio']]);

            if (!$studio) {
                return $this->json("this studio does not exists", Response::HTTP_NOT_FOUND);
            }
            $anime->setStudio($studio);
        }
        $manager->persist($anime);
        $manager->flush();
        return $this->json($anime, Response::HTTP_CREATED, [], ['groups' => 'anime']);
    }

    #[Route('/{id}/edit', methods: ['PUT'])]
    public function edit(Anime $anime, Request $request, EntityManagerInterface $manager, SerializerInterface $serializer, GenreRepository $genreRepository, StudioRepository $studioRepository): Response
    {
        $editedAnime = $serializer->deserialize($request->getContent(), Anime::class, 'json', ['object_to_populate' => $anime, 'groups' => 'anime-create']);
        if ($this->getUser() !== $anime->getEntryAuthor()) {
            return $this->json("you can't edit this anime, it's not yours", Response::HTTP_FORBIDDEN);
        }
        $editedAnime->setEntryAuthor($this->getUser());
        $dataEdited = json_decode($request->getContent(), true);

        if (isset($dataEdited['genres'])) {
            foreach ($dataEdited['genres'] as $genreName) {
                $genre = $genreRepository->findOneBy(['name' => $genreName]);

                if (!$genre) {
                    return $this->json("The genre '$genreName' does not exist", Response::HTTP_NOT_FOUND);
                }

                $editedAnime->addGenre($genre);
            }
        }
        if (isset($dataEdited['studio'])) {
            $studio = $studioRepository->findOneBy(['name' => $dataEdited['studio']]);

            if (!$studio) {
                return $this->json("this studio does not exists", Response::HTTP_NOT_FOUND);
            }
            $editedAnime->setStudio($studio);
        }
        $manager->flush();
        return $this->json("Anime edited", Response::HTTP_OK);
    }

    #[Route('/{id}/delete', methods: ['DELETE'])]
    public function delete(Anime $anime, EntityManagerInterface $manager): Response
    {
        if ($this->getUser() !== $anime->getEntryAuthor()) {
            return $this->json("you can't delete this anime, it's not yours", Response::HTTP_FORBIDDEN);
        }
        $manager->remove($anime);
        $manager->flush();
        return $this->json("Anime deleted", Response::HTTP_NO_CONTENT);
    }
}
