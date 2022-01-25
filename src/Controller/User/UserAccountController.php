<?php

namespace App\Controller\User;

use App\Form\User\UserPasswordType;
use App\Form\User\UserType;
use App\Repository\Paste\PasteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

#[Route(path: '/account')]
#[isGranted('ROLE_USER')]
class UserAccountController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em, private UserPasswordHasherInterface $passwordHasher, private PasteRepository $pasteRepository, private PaginatorInterface $paginator)
    {
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route(path: '/', name: 'user.account')]
    public function index(Request $request): Response
    {
        $accountForm = $this->createForm(UserType::class, $this->getUser());
        $accountForm->remove('password')->remove('pseudo');
        $accountForm->add('currentPassword', PasswordType::class, [
            'label' => 'Mot de passe actuel',
            'attr' => ['placeholder' => 'Entrez votre mot de passe actuel'],
            'constraints' => [new UserPassword()],
            'mapped' => false
        ]);
        $accountForm->handleRequest($request);

        if ($accountForm->isSubmitted() && $accountForm->get('avatarUrl')->getData()) {
            $fileInfo = pathinfo($accountForm->get('avatarUrl')->getData());
            $extension = $fileInfo['extension'];
            if ($extension && ($extension !== 'png' && $extension !== 'jpg')) {
                $accountForm->get('avatarUrl')->addError(new FormError('Votre image doit obligatoirement être au format .png ou .jpg'));
            } elseif(!$extension) {
                $accountForm->get('avatarUrl')->addError(new FormError('Impossible de déterminer l\'extension de l\'image.'));
            }
        }

        if ($accountForm->isSubmitted() && $accountForm->isValid()) {
            $this->em->flush();
            $this->addFlash('account_success', 'Votre compte a bien été mis à jour.');
            return $this->redirectToRoute('user.account');
        }

        $passwordForm = $this->createForm(UserPasswordType::class);
        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $this->getUser()->setPassword($this->passwordHasher->hashPassword($this->getUser(), $passwordForm->get('_password')->getData()));
            $this->em->flush();
            $this->addFlash('account_success', 'Votre mot de passe a bien été mis à jour.');
            return $this->redirectToRoute('user.account');
        }

        return $this->render('user/account.html.twig', [
            'current_page' => ['user_account'],
            'accountForm' => $accountForm->createView(),
            'passwordForm' => $passwordForm->createView()
        ]);
    }

    #[Route(path: '/mypastes', name: 'user.pastes')]
    public function listPastes(Request $request): Response
    {
        $pastes = $this->paginator->paginate(
            $this->pasteRepository->findBy(['user' => $this->getUser()], ['createdAt' => 'DESC']),
            $request->query->getInt('page', 1),
            20
        );
        return $this->render('user/list.pastes.html.twig', [
            'current_page' => ['user_pastes_list'],
            'pastes' => $pastes
        ]);
    }
}