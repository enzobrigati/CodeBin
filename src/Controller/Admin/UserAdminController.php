<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\User\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Route(path: '/admin')]
class UserAdminController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em, private UserRepository $userRepository)
    {
    }

    #[Route(path: '/users', name: 'admin.users.list')]
    public function listUsers(): Response
    {
        $users = $this->userRepository->findAll();
        return $this->render('admin/users.list.html.twig', [
            'users' => $users
        ]);
    }

    #[Route(path: '/user/edit/{id}', name: 'admin.user.edit', requirements: ['id' => '[0-9]*'])]
    public function editUser(User $user, Request $request): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->add('roles', ChoiceType::class, [
           'label' => 'Rôle',
            'choices' => [
                'Membre' => 'ROLE_USER',
                'Modérateur' => 'ROLE_MODERATOR',
                'Super Administrateur' => 'ROLE_SUPERADMIN',
            ],
            'constraints' => [new NotBlank()],
            'mapped' => false
        ]);
        $form->remove('password');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($user === $this->getUser() && $this->isGranted('ROLE_SUPERADMIN')) {
                $user->setRoles(['ROLE_SUPERADMIN']);
            } else {
                $user->setRoles([$form->get('roles')->getData()]);
            }
            $this->em->flush();
            $this->addFlash('admin_user_success', 'L\'utilisateur a bien été modifié.');
            return $this->redirectToRoute('admin.user.edit', ['id' => $user->getId()]);
        }

        return $this->render('admin/user.edit.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/user/delete/{id}', name: 'admin.user.delete', requirements: ['id' => '[0-9]*'])]
    public function deleteUser(User $user): Response
    {
       $this->em->remove($user);
       $this->em->flush();
       $this->addFlash('admin_users_success', 'L\'utilisateur a bien été supprimé.');
       return $this->redirectToRoute('admin.users.list');
    }

}