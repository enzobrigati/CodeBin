<?php

namespace App\Command\Security;

use App\Repository\PasswordResetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanPasswordRequest extends Command
{
    protected static $defaultName = 'cron:cleanpasswordrequest';
    private PasswordResetRepository $passwordResetRepository;
    private EntityManagerInterface $em;

    public function __construct(PasswordResetRepository $passwordResetRepository, EntityManagerInterface $em)
    {
        $this->passwordResetRepository = $passwordResetRepository;
        $this->em = $em;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Clean expired password reset tokens');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $now = new \DateTime();
        $passwordsReset = $this->passwordResetRepository->findAll();

        $output->writeln([
            'Nettoyage des tokens de réinitialisation des mots de passe expirés',
            '============',
        ]);

        try {
            foreach ($passwordsReset as $password) {
                if ($now > $password->getExpireAt()) {
                    $this->em->remove($password);
                }
            }
            $this->em->flush();
            $output->writeln([
                'Les tokens expirés ont bien été supprimé.',
            ]);
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln([
                'Impossible de supprimer les tokens expirés.',
                '',
                'Exception : ' . $e
            ]);
            return Command::FAILURE;
        }
    }
}