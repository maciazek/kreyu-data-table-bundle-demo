<?php

namespace App\Controller;

use App\DataTable\Filter\Formatter\DateRangeActiveFilterFormatter;
use App\DataTable\Type\HomepageDataTableType;
use App\Repository\EmployeeRepository;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomepageController extends AbstractController
{
    use DataTableFactoryAwareTrait;

    #[Route('/', name: 'app_homepage_index')]
    public function index(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $queryBuilder = $employeeRepository->createQueryBuilder('employee')
            ->leftJoin('employee.currentContract', 'currentContract')
            ->addSelect('currentContract')
        ;

        $dataTable = $this->createDataTable(HomepageDataTableType::class, $queryBuilder);
        $dataTable->handleRequest($request);

        if ($dataTable->isExporting()) {
            return $this->file($dataTable->export());
        }

        return $this->render('homepage/index.html.twig', [
            'employees' => $dataTable->createView(),
            'source_code_classes' => [
                HomepageDataTableType::class,
                DateRangeActiveFilterFormatter::class,
            ],
        ]);
    }
}
