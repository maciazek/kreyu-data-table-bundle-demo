<?php

namespace App\Controller;

use App\DataTable\Type\Experimental\ExperimentalDefaultFiltersDataTableType;
use App\Repository\EmployeeRepository;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/experimental')]
final class ExperimentalController extends AbstractController
{
    use DataTableFactoryAwareTrait;

    #[Route('/default_filters', name: 'app_experimental_default_filters')]
    public function defaultFilters(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $queryBuilder = $employeeRepository->createQueryBuilder('employee')
            ->leftJoin('employee.currentContract', 'currentContract')
            ->leftJoin('currentContract.title', 'currentContractTitle')
            ->addSelect('currentContract')
            ->addSelect('currentContractTitle')
        ;

        $dataTable = $this->createDataTable(ExperimentalDefaultFiltersDataTableType::class, $queryBuilder, options: [
            'themes' => [
                $request->getSession()->get('_data_table_theme')->getPath(),
                $request->getSession()->get('_data_table_icon_theme')->getPath(),
            ],
        ]);
        $dataTable->handleRequest($request);

        if ($dataTable->isExporting()) {
            return $this->file($dataTable->export());
        }

        return $this->render('experimental/default_filters.html.twig', [
            'employees' => $dataTable->createView(),
            'source_code_classes' => [
                ExperimentalDefaultFiltersDataTableType::class,
            ],
        ]);
    }
}
