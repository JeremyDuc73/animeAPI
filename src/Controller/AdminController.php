<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(UserRepository $repository): Response
    {
        $users = $repository->findAll();
        return $this->render('admin/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/{id}/change-status', name: 'app_change_account_status')]
    public function changeStatus(User $user, EntityManagerInterface $manager): Response
    {
        if ($user->isStatus()) {
            $user->setStatus(false);
            $roles = $user->getRoles();
            if (in_array('ROLE_USER', $roles)) {
                $roles = array_diff($roles, ['ROLE_USER']);
                $user->setRoles($roles);
            }
        } else {
            $user->setStatus(true);
            $roles = $user->getRoles();
            if (!in_array('ROLE_USER', $roles)) {
                $roles[] = 'ROLE_USER';
                $user->setRoles($roles);
            }
        }
        $manager->flush();
        return $this->redirectToRoute('app_admin');
    }
}
