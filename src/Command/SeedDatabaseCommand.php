<?php

namespace App\Command;

use App\Entity\Contract;
use App\Entity\Employee;
use App\Enum\EmployeeStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:seed-database',
    description: 'Fill database with fake data',
)]
class SeedDatabaseCommand extends Command
{
    const COUNTS = [
        '_page' => 25,
        'employee' => 150,
    ];

    public function __construct(
        #[Autowire(env: 'APP_DEFAULT_LOCALE')] private string $locale,
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $io = new SymfonyStyle($input, $output);
        $faker = \Faker\Factory::create($this->locale);

        // employees
        for ($i = 1; $i <= self::COUNTS['employee']; $i++) {
            $employee = new Employee();
            $employee->setFirstName($faker->firstName());
            $employee->setLastName($faker->lastName());
            $employee->setBirthDate($faker->dateTimeInInterval('-60 years', '+35 years'));
            $employee->setStatus($faker->randomElement(EmployeeStatus::class));
            $this->entityManager->persist($employee);

            // contracts
            for ($j = 1; $j <= $faker->numberBetween(1, 4); $j++) {
                $contract = new Contract();
                $contract->setEmployee($employee);
                $contract->setSalary($faker->randomFloat(2, 4000, 16000));
                $contract->setBeginDate($faker->dateTimeInInterval('-15 years', '+5 years'));
                $contract->setEndDate($faker->numberBetween(0, 2) === 0 ? null : $faker->dateTimeInInterval('-10 years', '+8 years'));
                $this->entityManager->persist($contract);
            }

            if ($i % self::COUNTS['_page'] === 0) {
                $this->entityManager->flush();
                $this->entityManager->clear();
            }
        }
        $this->entityManager->flush();
        $this->entityManager->clear();

        $io->success('Database seeded successfully!');

        return Command::SUCCESS;
    }
}
