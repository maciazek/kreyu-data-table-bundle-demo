<?php

namespace App\Controller;

use App\DataTable\Type\Filter\FilterDoctrineOrmDataTableType;
use App\DataTable\Type\Filter\FilterEventsDataTableType;
use App\DataTable\Type\Filter\FilterExpressionTransformersDataTableType;
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

    #[Route('/doctrine_orm', name: 'app_filter_doctrine_orm')]
    public function doctrineOrm(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $queryBuilder = $employeeRepository->createQueryBuilder('employee')
            ->leftJoin('employee.currentContract', 'currentContract')
            ->leftJoin('currentContract.title', 'currentContractTitle')
            ->addSelect('currentContract')
            ->addSelect('currentContractTitle')
        ;

        $dataTable = $this->createDataTable(FilterDoctrineOrmDataTableType::class, $queryBuilder, options: [
            'themes' => [
                $request->getSession()->get('_data_table_theme')->getPath(),
                $request->getSession()->get('_data_table_icon_theme')->getPath(),
            ],
        ]);
        $dataTable->handleRequest($request);

        if ($dataTable->isExporting()) {
            return $this->file($dataTable->export());
        }

        return $this->render('filter/doctrine_orm.html.twig', [
            'employees' => $dataTable->createView(),
            'source_code_classes' => [
                FilterDoctrineOrmDataTableType::class,
            ],
        ]);
    }

    #[Route('/search_simple', name: 'app_filter_search_simple')]
    public function searchSimple(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $queryBuilder = $employeeRepository->createQueryBuilder('employee')
            ->leftJoin('employee.currentContract', 'currentContract')
            ->leftJoin('currentContract.title', 'currentContractTitle')
            ->addSelect('currentContract')
            ->addSelect('currentContractTitle')
        ;

        $dataTable = $this->createDataTable(FilterSearchSimpleDataTableType::class, $queryBuilder, options: [
            'themes' => [
                $request->getSession()->get('_data_table_theme')->getPath(),
                $request->getSession()->get('_data_table_icon_theme')->getPath(),
            ],
        ]);
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

        $dataTable = $this->createDataTable(FilterSearchAdvancedDataTableType::class, $queryBuilder, options: [
            'themes' => [
                $request->getSession()->get('_data_table_theme')->getPath(),
                $request->getSession()->get('_data_table_icon_theme')->getPath(),
            ],
        ]);
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

    #[Route('/expression_transformers', name: 'app_filter_expression_transformers')]
    public function expressionTransformers(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $queryBuilder = $employeeRepository->createQueryBuilder('employee');

        $dataTable = $this->createDataTable(FilterExpressionTransformersDataTableType::class, $queryBuilder, options: [
            'themes' => [
                $request->getSession()->get('_data_table_theme')->getPath(),
                $request->getSession()->get('_data_table_icon_theme')->getPath(),
            ],
        ]);
        $dataTable->handleRequest($request);

        if ($dataTable->isExporting()) {
            return $this->file($dataTable->export());
        }

        return $this->render('filter/expression_transformers.html.twig', [
            'employees' => $dataTable->createView(),
            'source_code_classes' => [
                FilterExpressionTransformersDataTableType::class,
            ],
        ]);
    }

    #[Route('/events', name: 'app_filter_events')]
    public function events(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $queryBuilder = $employeeRepository->createQueryBuilder('employee')
            ->leftJoin('employee.address', 'address')
            ->leftJoin('address.city', 'city')
            ->addSelect('address')
            ->addSelect('city')
        ;

        $dataTable = $this->createDataTable(FilterEventsDataTableType::class, $queryBuilder, options: [
            'themes' => [
                $request->getSession()->get('_data_table_theme')->getPath(),
                $request->getSession()->get('_data_table_icon_theme')->getPath(),
            ],
        ]);
        $dataTable->handleRequest($request);

        if ($dataTable->isExporting()) {
            return $this->file($dataTable->export());
        }

        return $this->render('filter/events.html.twig', [
            'employees' => $dataTable->createView(),
            'source_code_classes' => [
                FilterEventsDataTableType::class,
            ],
        ]);
    }
}
