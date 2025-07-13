<?php

namespace App\Controller;

use App\DataTable\Filter\Formatter\DateRangeActiveFilterFormatter;
use App\DataTable\Type\HomepageDataTableType;
use App\Enum\DataTableIconTheme;
use App\Enum\DataTableTheme;
use App\Enum\EmployeeStatus;
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
            ->leftJoin('currentContract.title', 'currentContractTitle')
            ->addSelect('currentContract')
            ->addSelect('currentContractTitle')
        ;

        $dataTable = $this->createDataTable(HomepageDataTableType::class, $queryBuilder, options: [
            'themes' => [
                $request->getSession()->get('_data_table_theme')->getPath(),
                $request->getSession()->get('_data_table_icon_theme')->getPath(),
            ],
        ]);
        $dataTable->handleRequest($request);

        if ($dataTable->isExporting()) {
            return $this->file($dataTable->export());
        }

        return $this->render('homepage/index.html.twig', [
            'employees' => $dataTable->createView(),
            'source_code_classes' => [
                HomepageDataTableType::class,
                EmployeeStatus::class,
                DateRangeActiveFilterFormatter::class,
            ],
        ]);
    }
}
