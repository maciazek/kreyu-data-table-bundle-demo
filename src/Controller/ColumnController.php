<?php

namespace App\Controller;

use App\DataTable\Type\Column\ColumnBasicOptionsDataTableType;
use App\DataTable\Type\Column\ColumnDateDataTableType;
use App\DataTable\Type\Column\ColumnDateTimeDataTableType;
use App\DataTable\Type\Column\ColumnEnumDataTableType;
use App\DataTable\Type\Column\ColumnMoneyDataTableType;
use App\DataTable\Type\Column\ColumnTextDataTableType;
use App\Repository\EmployeeRepository;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/column')]
final class ColumnController extends AbstractController
{
    use DataTableFactoryAwareTrait;

    #[Route('/basic_options', name: 'app_column_basic_options')]
    public function basicOptions(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $queryBuilder = $employeeRepository->createQueryBuilder('employee');

        $dataTable = $this->createDataTable(ColumnBasicOptionsDataTableType::class, $queryBuilder);
        $dataTable->handleRequest($request);

        if ($dataTable->isExporting()) {
            return $this->file($dataTable->export());
        }

        return $this->render('column/basic_options.html.twig', [
            'employees' => $dataTable->createView(),
            'source_code_classes' => [
                ColumnBasicOptionsDataTableType::class,
            ],
        ]);
    }

    #[Route('/text', name: 'app_column_text')]
    public function text(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $queryBuilder = $employeeRepository->createQueryBuilder('employee');

        $dataTable = $this->createDataTable(ColumnTextDataTableType::class, $queryBuilder);
        $dataTable->handleRequest($request);

        if ($dataTable->isExporting()) {
            return $this->file($dataTable->export());
        }

        return $this->render('column/text.html.twig', [
            'employees' => $dataTable->createView(),
            'source_code_classes' => [
                ColumnTextDataTableType::class,
            ],
        ]);
    }

    #[Route('/money', name: 'app_column_money')]
    public function money(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $queryBuilder = $employeeRepository->createQueryBuilder('employee')
            ->leftJoin('employee.currentContract', 'currentContract')
            ->addSelect('currentContract')
        ;

        $dataTable = $this->createDataTable(ColumnMoneyDataTableType::class, $queryBuilder);
        $dataTable->handleRequest($request);

        if ($dataTable->isExporting()) {
            return $this->file($dataTable->export());
        }

        return $this->render('column/money.html.twig', [
            'employees' => $dataTable->createView(),
            'source_code_classes' => [
                ColumnMoneyDataTableType::class,
            ],
        ]);
    }

    #[Route('/date', name: 'app_column_date')]
    public function date(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $queryBuilder = $employeeRepository->createQueryBuilder('employee');

        $dataTable = $this->createDataTable(ColumnDateDataTableType::class, $queryBuilder);
        $dataTable->handleRequest($request);

        if ($dataTable->isExporting()) {
            return $this->file($dataTable->export());
        }

        return $this->render('column/date.html.twig', [
            'employees' => $dataTable->createView(),
            'source_code_classes' => [
                ColumnDateDataTableType::class,
            ],
        ]);
    }

    #[Route('/date_time', name: 'app_column_date_time')]
    public function dateTime(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $queryBuilder = $employeeRepository->createQueryBuilder('employee');

        $dataTable = $this->createDataTable(ColumnDateTimeDataTableType::class, $queryBuilder);
        $dataTable->handleRequest($request);

        if ($dataTable->isExporting()) {
            return $this->file($dataTable->export());
        }

        return $this->render('column/date_time.html.twig', [
            'employees' => $dataTable->createView(),
            'source_code_classes' => [
                ColumnDateTimeDataTableType::class,
            ],
        ]);
    }

    #[Route('/enum', name: 'app_column_enum')]
    public function enum(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $queryBuilder = $employeeRepository->createQueryBuilder('employee');

        $dataTable = $this->createDataTable(ColumnEnumDataTableType::class, $queryBuilder);
        $dataTable->handleRequest($request);

        if ($dataTable->isExporting()) {
            return $this->file($dataTable->export());
        }

        return $this->render('column/enum.html.twig', [
            'employees' => $dataTable->createView(),
            'source_code_classes' => [
                ColumnEnumDataTableType::class,
            ],
        ]);
    }
}
