<?php

namespace App\Controller\Security;

use App\Entity\PasswordReset;
use App\Form\User\PasswordResetRequestType;
use App\Form\User\PasswordResetType;
use App\Repository\PasswordResetRepository;
use App\Repository\UserRepository;
use App\Services\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PasswordResetController extends AbstractController
{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /**
     * @Route("/password/reset", name="app_password_reset_request")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param MailerService $mailerService
     * @param UrlGeneratorInterface $urlGenerator
     * @return Response
     * @throws Exception
     */
    public function index(Request $request, UserRepository $userRepository, MailerService $mailerService, UrlGeneratorInterface $urlGenerator): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('main');
        }
        $form = $this->createForm(PasswordResetRequestType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findOneBy(['email' => $form->get('_email')->getData()]);
            if ($user) {

                if ($user->getPasswordResetRequest()) {
                    $this->em->remove($user->getPasswordResetRequest());
                    $this->em->flush();
                    $this->em->refresh($user); //refresh pour éviter que le token soit toujours présent
                }

                $token = random_bytes(20);
                $hashed_token = hash('sha256', $token);
                $now = new \DateTime();
                $expiration = new \DateTime();

                $passwordReset = new PasswordReset();
                $passwordReset
                    ->setUser($user)
                    ->setHashedToken($hashed_token)
                    ->setRequestedAt($now)
                    ->setExpireAt($expiration->add(new \DateInterval("PT15M")));
                $this->em->persist($passwordReset);
                $this->em->flush();

                $resetUrl = $urlGenerator->generate('app_password_reset_token', ['hashed_token' => $hashed_token]);

                $mailerService->sendMessage(
                    'Demande de réinitialisation de mot de passe -  CodeBin',
                    $user->getEmail(),
                    'emails/password/notify.password_reset_request.html.twig',
                    ['resetUrl' => 'https://codebin.deob.fr'.$resetUrl]
                );

            }
            $this->addFlash('password_reset_request_sent', 'Si votre adresse email est correcte vous devriez recevoir un lien de réintialisation dans quelques minutes. Pensez à vérifier vos spams.');
            return $this->redirectToRoute('app_password_reset_request');
        }

        return $this->render('security/password_reset/request.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/password/reset/{hashed_token}", name="app_password_reset_token")
     * @param Request $request
     * @param string $hashed_token
     * @param UserPasswordHasherInterface $passwordHasher
     * @param MailerService $mailerService
     * @param PasswordResetRepository $passwordResetRepository
     * @return Response
     */
    public function reset(Request $request, string $hashed_token, UserPasswordHasherInterface $passwordHasher, MailerService $mailerService, PasswordResetRepository $passwordResetRepository): Response
    {
        $passwordReset = $passwordResetRepository->findOneBy(['hashedToken' => $hashed_token]);
        if(!$passwordReset) {
            return $this->redirectToRoute('main');
        }
        $now = new \DateTime();
        if ($now > $passwordReset->getExpireAt()) {
            $this->em->remove($passwordReset);
            $this->em->flush();
            $this->addFlash('password_reset_request_error', 'Ce token a expiré. Merci d\'effectuer une nouvelle demande de réinitialisation');
            return $this->redirectToRoute('app_password_reset_request');
        }

        $form = $this->createForm(PasswordResetType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $new_password = $form->get('_newpassword')->getData();
            $new_password_encoded = $passwordHasher->hashPassword($passwordReset->getUser(), $new_password);
            $passwordReset->getUser()->setPassword($new_password_encoded);
            $this->em->remove($passwordReset);
            $this->em->flush();

            $mailerService->sendMessage(
                'Votre mot de passe vient d\'être modifié - CodeBin',
                $passwordReset->getUser()->getEmail(),
                'emails/password/notify.password_change.html.twig',
                ['resetUrl' => 'https://codebin.deob.fr'.$this->generateUrl('app_password_reset_request')]
            );

            $this->addFlash('password_reset_success', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/password_reset/reset.html.twig', [
            'form' => $form->createView()
        ]);
    }

}