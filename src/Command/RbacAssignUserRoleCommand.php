<?php

namespace Triforce\RBACBundle\Command;

use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Triforce\RBACBundle\Core\Manager\RoleManager;
use Triforce\RBACBundle\Repository\PermissionRepository;
use Triforce\RBACBundle\Repository\RoleRepository;

#[AsCommand(
    name: 'security:rbac:user:assign-role',
    description: 'Assign roles to a user',
)]
class RbacAssignUserRoleCommand extends Command
{
    public function __construct(
        private PermissionRepository $permissionRepository,
        private RoleRepository $roleRepository,
        private RoleManager $roleManager,
        private UserRepository $userRepository
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('security:rbac:user:assign-role')
            ->setDescription('Assign roles to a user')
            ->addArgument('userId', InputArgument::REQUIRED, "The user Id");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        $rolesTmp = $this->roleRepository->findAll();
        $roles = [];
        foreach ($rolesTmp as $role) {
            $pathNodes = $this->roleRepository->getPath($role->getId());
            $path = "/" . implode('/', $pathNodes);
            $path = str_replace("/root", "/", $path);
            $path = str_replace("//", "/", $path);
            $roles[$path] = $role;
        }
        ksort($roles);

        $userId = $input->getArgument('userId');

        $user = $this->userRepository->find($userId);

        $question = new ChoiceQuestion('Choice the roles (multiple separate by comma): ', array_keys($roles), 0);
        $question->setMultiselect(true);
        $rolePaths = $helper->ask($input, $output, $question);
        foreach ($rolePaths as $rolePath) {
            $role = $roles[$rolePath];
            $user->addRbacRole($role);
        }
        $this->userRepository->add($user, true);

        return Command::SUCCESS;
    }
}
