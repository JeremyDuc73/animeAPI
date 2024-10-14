<?php

namespace App\Controller;

use App\Entity\Anime;
use App\Repository\AnimeRepository;
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
        return $this->json($animeRepository->findAll());
    }

    #[Route('/add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer): Response
    {
        $anime = $serializer->deserialize($request->getContent(), Anime::class, 'json');
        $anime->setEntryAuthor($this->getUser());
        $manager->persist($anime);
        $manager->flush();
        return $this->json($anime, Response::HTTP_CREATED);
    }
}
