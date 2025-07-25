<?php

namespace App\Command;

use App\Entity\Address;
use App\Entity\City;
use App\Entity\Contract;
use App\Entity\Employee;
use App\Entity\Target;
use App\Entity\Title;
use App\Enum\EmployeeRole;
use App\Enum\EmployeeStatus;
use App\Repository\CityRepository;
use App\Repository\TitleRepository;
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
    private const COUNTS = [
        '_page' => 25,
        'title' => 25,
        'city' => 25,
        'employee' => 500,
    ];

    private const LOCALE_MAP = [
        'en' => 'en_US',
        'pl' => 'pl_PL',
    ];

    public function __construct(
        #[Autowire(env: 'APP_DEFAULT_LOCALE')] private string $locale,
        private EntityManagerInterface $entityManager,
        private TitleRepository $titleRepository,
        private CityRepository $cityRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $faker = \Faker\Factory::create(self::LOCALE_MAP[$this->locale]);

        // titles
        for ($i = 1; $i <= self::COUNTS['title']; $i++) {
            $title = new Title();
            $title->setName($faker->unique()->jobTitle());
            $this->entityManager->persist($title);

            if ($i % self::COUNTS['_page'] === 0) {
                $this->entityManager->flush();
                $this->entityManager->clear();
            }
        }
        $this->entityManager->flush();
        $this->entityManager->clear();

        // cities
        for ($i = 1; $i <= self::COUNTS['city']; $i++) {
            $city = new City();
            $city->setName($faker->unique()->city());
            $this->entityManager->persist($city);

            if ($i % self::COUNTS['_page'] === 0) {
                $this->entityManager->flush();
                $this->entityManager->clear();
            }
        }
        $this->entityManager->flush();
        $this->entityManager->clear();

        // employees
        for ($i = 1; $i <= self::COUNTS['employee']; $i++) {
            $titles = $this->titleRepository->findAll();
            $cities = $this->cityRepository->findAll();

            $employee = new Employee();
            $employee->setFirstName($faker->firstName());
            $employee->setLastName($faker->lastName());
            $employee->setBirthDate(\DateTimeImmutable::createFromMutable($faker->dateTimeInInterval('-60 years', '+35 years')));
            $employee->setLastLoginAt($faker->numberBetween(0, 3) === 0 ? null : \DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-5 years')));
            $employee->setStatus($faker->randomElement(EmployeeStatus::class));
            if ($employee->getStatus() !== EmployeeStatus::INACTIVE) {
                $employee->setWebsite($faker->numberBetween(0, 3) === 0 ? null : $faker->url());
                $employee->setRoles($faker->randomElements(EmployeeRole::class, $faker->numberBetween(0, count(EmployeeRole::cases()))));
            }

            $this->entityManager->persist($employee);

            // address
            if ($employee->getStatus() !== EmployeeStatus::INACTIVE) {
                $address = new Address();
                $address->setStreetName($faker->streetName());
                $address->setBuildingNumber($faker->buildingNumber());
                $address->setPostcode($faker->postcode());
                $address->setCity($faker->randomElement($cities));
                $this->entityManager->persist($address);
                $employee->setAddress($address);
            }

            // contracts
            for ($j = 1; $j <= $faker->numberBetween(1, 4); $j++) {
                $contract = new Contract();
                $contract->setEmployee($employee);
                $contract->setTitle($faker->randomElement($titles));
                $contract->setSalaryInCents($faker->numberBetween(400000, 1600000));
                $contract->setSalary((float) $contract->getSalaryInCents() / 100);
                $contract->setBeginDate(\DateTimeImmutable::createFromMutable($faker->dateTimeInInterval('-15 years', '+5 years')));
                $contract->setEndDate($faker->numberBetween(0, 2) === 0 ? null : \DateTimeImmutable::createFromMutable($faker->dateTimeInInterval('-10 years', '+8 years')));
                $this->entityManager->persist($contract);

                if ($employee->getStatus() !== EmployeeStatus::INACTIVE) {
                    $employee->setCurrentContract($contract);
                    $employee->setIsManager($faker->boolean(33));
                }

                // targets
                $end = $contract->getEndDate()?->modify('first day of this month') ?? new \DateTimeImmutable('first day of this month');
                $begin = $end->modify('-1 year');
                $period = new \DatePeriod($begin, \DateInterval::createFromDateString('1 month'), $end);
                foreach ($period as $month) {
                    $target = new Target();
                    $target->setContract($contract);
                    $target->setMonth($month);
                    $target->setValue($faker->numberBetween(0, 4) === 0 ? 100 : $faker->numberBetween(10, 100));
                    $target->setValueDecimal((float) $target->getValue() / 100);
                    $this->entityManager->persist($target);

                    $contract->setCurrentTarget($target);
                }
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
