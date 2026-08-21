<?php
namespace App\Command;
use App\Entity\AdminUser;
use App\Repository\AdminUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:create-admin', description: 'Crée un compte administrateur FyraCare')]
class CreateAdminCommand extends Command
{
    public function __construct(private EntityManagerInterface $em, private AdminUserRepository $users, private UserPasswordHasherInterface $hasher) { parent::__construct(); }
    protected function configure(): void { $this->addArgument('email', InputArgument::REQUIRED, 'Adresse e-mail'); }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (string) $input->getArgument('email');
        if ($this->users->findOneBy(['email' => mb_strtolower($email)])) { $output->writeln('<error>Ce compte existe déjà.</error>'); return Command::FAILURE; }
        $question = (new Question('Mot de passe : '))->setHidden(true)->setHiddenFallback(false);
        $password = (string) $this->getHelper('question')->ask($input, $output, $question);
        if (mb_strlen($password) < 10) { $output->writeln('<error>Utilisez au moins 10 caractères.</error>'); return Command::FAILURE; }
        $user = (new AdminUser())->setEmail($email);
        $user->setPassword($this->hasher->hashPassword($user, $password));
        $this->em->persist($user); $this->em->flush();
        $output->writeln('<info>Compte administrateur créé.</info>'); return Command::SUCCESS;
    }
}
