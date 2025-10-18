<?php

namespace App\Command;

use App\Entity\User;
use App\Exception\UserAlreadyExistException;
use App\Form\Role;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


#[AsCommand(
    name: 'app:add-user',
    description: 'Add a short description for your command',
)]
class AddUserCommand extends Command
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $userPasswordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $roleLabels = [
            'Administrator' => Role::ADMIN,
            'User'          => Role::USER,
        ];
        $io->title('Add a short description for your command');

        $email = $io->ask('Enter email address');

        $role = $io->choice('Choose role', array_keys($roleLabels), false);
        $role = $roleLabels[$role];

        $password = $io->askHidden('Enter password');

        $fullName = $io->ask('Enter full name');

        try {
           $user = $this->createUser($email, $password, $role, $fullName);

            $io->success('User created');
        } catch (UserAlreadyExistException $e) {
            $io->error($e->getDomainError());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function createUser(string $email, string $password, Role $role, string $fullName)
    {
        $existingUser = $this->userRepository->findOneBy(['email' => $email]);

        if ($existingUser) {
            throw new RuntimeException('User already exists');
        }

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $password));
        $user->setRoles([$role->value]);
        $user->setIsVerified(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
