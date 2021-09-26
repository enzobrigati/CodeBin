<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\User\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em, private UserRepository $userRepository) {}

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('main');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->remove('avatarUrl');
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $existingUser = $this->userRepository->findByEmailOrUsername($user->getEmail(), $user->getPseudo());
            if($existingUser) {
               $this->addFlash('register_error', 'Le nom d\'utilisateur ou l\'adresse email est déjà utilisée.');
                return $this->redirectToRoute('app_register');
            }
            $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('register_success', 'Votre compte a bien été créé. Vous pouvez dès à présent vous connecter.');
            return $this->redirectToRoute('app_login');
        }
        return $this->render('security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        return $this->redirectToRoute('main');
    }
}
