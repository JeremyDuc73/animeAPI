<?php

namespace App\Controller;

use App\Entity\Studio;
use App\Repository\StudioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/studio')]
class StudioController extends AbstractController
{
    #[Route('s', methods: ['GET'])]
    public function index(StudioRepository $studioRepository): Response
    {
        return $this->json($studioRepository->findAll(), Response::HTTP_OK, [], ['groups' => 'studio']);
    }

    #[Route('/add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer): Response
    {
        $studio = $serializer->deserialize($request->getContent(), Studio::class, 'json');
        $studio->setEntryAuthor($this->getUser());
        $manager->persist($studio);
        $manager->flush();
        return $this->json($studio, Response::HTTP_CREATED, [], ['groups' => 'studio']);
    }

    #[Route('/{id}/edit', methods: ['PUT'])]
    public function edit(Studio $studio, Request $request, EntityManagerInterface $manager, SerializerInterface $serializer): Response
    {
        $editedStudio = $serializer->deserialize($request->getContent(), Studio::class, 'json', ['object_to_populate' => $studio]);
        if ($this->getUser() !== $studio->getEntryAuthor()) {
            return $this->json("you can't edit this studio, it's not yours", Response::HTTP_FORBIDDEN);
        }
        $editedStudio->setEntryAuthor($this->getUser());
        $manager->flush();
        return $this->json("Studio edited", Response::HTTP_OK);
    }

    #[Route('/{id}/delete', methods: ['DELETE'])]
    public function delete(Studio $studio, EntityManagerInterface $manager): Response
    {
        if ($this->getUser() !== $studio->getEntryAuthor()) {
            return $this->json("you can't delete this studio, it's not yours", Response::HTTP_FORBIDDEN);
        }
        $manager->remove($studio);
        $manager->flush();
        return $this->json("Studio deleted", Response::HTTP_NO_CONTENT);
    }
}
