<?php

namespace App\Controller;

use App\DataTable\Type\Filter\FilterSearchAdvancedDataTableType;
use App\DataTable\Type\Filter\FilterSearchSimpleDataTableType;
use App\Repository\EmployeeRepository;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/filter')]
final class FilterController extends AbstractController
{
    use DataTableFactoryAwareTrait;

    #[Route('/search_simple', name: 'app_filter_search_simple')]
    public function searchSimple(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $queryBuilder = $employeeRepository->createQueryBuilder('employee')
            ->leftJoin('employee.currentContract', 'currentContract')
            ->leftJoin('currentContract.title', 'currentContractTitle')
            ->addSelect('currentContract')
            ->addSelect('currentContractTitle')
        ;

        $dataTable = $this->createDataTable(FilterSearchSimpleDataTableType::class, $queryBuilder);
        $dataTable->handleRequest($request);

        if ($dataTable->isExporting()) {
            return $this->file($dataTable->export());
        }

        return $this->render('filter/search_simple.html.twig', [
            'employees' => $dataTable->createView(),
            'source_code_classes' => [
                FilterSearchSimpleDataTableType::class,
            ],
        ]);
    }

    #[Route('/search_advanced', name: 'app_filter_search_advanced')]
    public function searchAdvanced(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $queryBuilder = $employeeRepository->createQueryBuilder('employee')
            ->leftJoin('employee.currentContract', 'currentContract')
            ->leftJoin('currentContract.title', 'currentContractTitle')
            ->addSelect('currentContract')
            ->addSelect('currentContractTitle')
        ;

        $dataTable = $this->createDataTable(FilterSearchAdvancedDataTableType::class, $queryBuilder);
        $dataTable->handleRequest($request);

        if ($dataTable->isExporting()) {
            return $this->file($dataTable->export());
        }

        return $this->render('filter/search_advanced.html.twig', [
            'employees' => $dataTable->createView(),
            'source_code_classes' => [
                FilterSearchAdvancedDataTableType::class,
            ],
        ]);
    }
}
