<?php

namespace App\Controller;

use App\DataTable\Type\Column\ColumnBasicOptionsDataTableType;
use App\DataTable\Type\Column\ColumnBooleanDataTableType;
use App\DataTable\Type\Column\ColumnCollectionDataTableType;
use App\DataTable\Type\Column\ColumnDateDataTableType;
use App\DataTable\Type\Column\ColumnDatePeriodDataTableType;
use App\DataTable\Type\Column\ColumnDateTimeDataTableType;
use App\DataTable\Type\Column\ColumnEnumDataTableType;
use App\DataTable\Type\Column\ColumnHtmlDataTableType;
use App\DataTable\Type\Column\ColumnIconDataTableType;
use App\DataTable\Type\Column\ColumnMoneyDataTableType;
use App\DataTable\Type\Column\ColumnTemplateDataTableType;
use App\DataTable\Type\Column\ColumnTextDataTableType;
use App\Enum\DataTableIconTheme;
use App\Enum\DataTableTheme;
use App\Enum\EmployeeRole;
use App\Enum\EmployeeStatus;
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

        $dataTable = $this->createDataTable(ColumnBasicOptionsDataTableType::class, $queryBuilder, options: [
            'themes' => [
                DataTableTheme::from($request->getSession()->get('_data_table_theme'))->getPath(),
                DataTableIconTheme::from($request->getSession()->get('_data_table_icon_theme'))->getPath(),
            ],
        ]);
        $dataTable->handleRequest($request);

        if ($dataTable->isExporting()) {
            return $this->file($dataTable->export());
        }

        return $this->render('column/basic_options.html.twig', [
            'employees' => $dataTable->createView(),
            'source_code_classes' => [
                ColumnBasicOptionsDataTableType::class,
                EmployeeStatus::class,
            ],
        ]);
    }

    #[Route('/text', name: 'app_column_text')]
    public function text(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $queryBuilder = $employeeRepository->createQueryBuilder('employee');

        $dataTable = $this->createDataTable(ColumnTextDataTableType::class, $queryBuilder, options: [
            'themes' => [
                DataTableTheme::from($request->getSession()->get('_data_table_theme'))->getPath(),
                DataTableIconTheme::from($request->getSession()->get('_data_table_icon_theme'))->getPath(),
            ],
        ]);
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

        $dataTable = $this->createDataTable(ColumnMoneyDataTableType::class, $queryBuilder, options: [
            'themes' => [
                DataTableTheme::from($request->getSession()->get('_data_table_theme'))->getPath(),
                DataTableIconTheme::from($request->getSession()->get('_data_table_icon_theme'))->getPath(),
            ],
        ]);
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

    #[Route('/boolean', name: 'app_column_boolean')]
    public function boolean(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $queryBuilder = $employeeRepository->createQueryBuilder('employee');

        $dataTable = $this->createDataTable(ColumnBooleanDataTableType::class, $queryBuilder, options: [
            'themes' => [
                DataTableTheme::from($request->getSession()->get('_data_table_theme'))->getPath(),
                DataTableIconTheme::from($request->getSession()->get('_data_table_icon_theme'))->getPath(),
            ],
        ]);
        $dataTable->handleRequest($request);

        if ($dataTable->isExporting()) {
            return $this->file($dataTable->export());
        }

        return $this->render('column/boolean.html.twig', [
            'employees' => $dataTable->createView(),
            'source_code_classes' => [
                ColumnBooleanDataTableType::class,
            ],
        ]);
    }

    #[Route('/date', name: 'app_column_date')]
    public function date(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $queryBuilder = $employeeRepository->createQueryBuilder('employee');

        $dataTable = $this->createDataTable(ColumnDateDataTableType::class, $queryBuilder, options: [
            'themes' => [
                DataTableTheme::from($request->getSession()->get('_data_table_theme'))->getPath(),
                DataTableIconTheme::from($request->getSession()->get('_data_table_icon_theme'))->getPath(),
            ],
        ]);
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

        $dataTable = $this->createDataTable(ColumnDateTimeDataTableType::class, $queryBuilder, options: [
            'themes' => [
                DataTableTheme::from($request->getSession()->get('_data_table_theme'))->getPath(),
                DataTableIconTheme::from($request->getSession()->get('_data_table_icon_theme'))->getPath(),
            ],
        ]);
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

    #[Route('/date_period', name: 'app_column_date_period')]
    public function datePeriod(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $queryBuilder = $employeeRepository->createQueryBuilder('employee')
            ->leftJoin('employee.currentContract', 'currentContract')
            ->addSelect('currentContract')
        ;

        $dataTable = $this->createDataTable(ColumnDatePeriodDataTableType::class, $queryBuilder, options: [
            'themes' => [
                DataTableTheme::from($request->getSession()->get('_data_table_theme'))->getPath(),
                DataTableIconTheme::from($request->getSession()->get('_data_table_icon_theme'))->getPath(),
            ],
        ]);
        $dataTable->handleRequest($request);

        if ($dataTable->isExporting()) {
            return $this->file($dataTable->export());
        }

        return $this->render('column/date_period.html.twig', [
            'employees' => $dataTable->createView(),
            'source_code_classes' => [
                ColumnDatePeriodDataTableType::class,
            ],
        ]);
    }

    #[Route('/collection', name: 'app_column_collection')]
    public function collection(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $queryBuilder = $employeeRepository->createQueryBuilder('employee')
            ->leftJoin('employee.contracts', 'contracts')
            ->addSelect('contracts')
        ;

        $dataTable = $this->createDataTable(ColumnCollectionDataTableType::class, $queryBuilder, options: [
            'themes' => [
                DataTableTheme::from($request->getSession()->get('_data_table_theme'))->getPath(),
                DataTableIconTheme::from($request->getSession()->get('_data_table_icon_theme'))->getPath(),
            ],
        ]);
        $dataTable->handleRequest($request);

        if ($dataTable->isExporting()) {
            return $this->file($dataTable->export());
        }

        return $this->render('column/collection.html.twig', [
            'employees' => $dataTable->createView(),
            'source_code_classes' => [
                ColumnCollectionDataTableType::class,
                EmployeeRole::class,
            ],
        ]);
    }

    #[Route('/enum', name: 'app_column_enum')]
    public function enum(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $queryBuilder = $employeeRepository->createQueryBuilder('employee');

        $dataTable = $this->createDataTable(ColumnEnumDataTableType::class, $queryBuilder, options: [
            'themes' => [
                DataTableTheme::from($request->getSession()->get('_data_table_theme'))->getPath(),
                DataTableIconTheme::from($request->getSession()->get('_data_table_icon_theme'))->getPath(),
            ],
        ]);
        $dataTable->handleRequest($request);

        if ($dataTable->isExporting()) {
            return $this->file($dataTable->export());
        }

        return $this->render('column/enum.html.twig', [
            'employees' => $dataTable->createView(),
            'source_code_classes' => [
                ColumnEnumDataTableType::class,
                EmployeeStatus::class,
            ],
        ]);
    }

    #[Route('/template', name: 'app_column_template')]
    public function template(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $queryBuilder = $employeeRepository->createQueryBuilder('employee')
            ->leftJoin('employee.currentContract', 'currentContract')
            ->leftJoin('currentContract.currentTarget', 'currentTarget')
            ->leftJoin('currentContract.targets', 'targets')
            ->addSelect('currentContract')
            ->addSelect('currentTarget')
            ->addSelect('targets')
        ;

        $dataTable = $this->createDataTable(ColumnTemplateDataTableType::class, $queryBuilder, options: [
            'themes' => [
                DataTableTheme::from($request->getSession()->get('_data_table_theme'))->getPath(),
                DataTableIconTheme::from($request->getSession()->get('_data_table_icon_theme'))->getPath(),
            ],
        ]);
        $dataTable->handleRequest($request);

        if ($dataTable->isExporting()) {
            return $this->file($dataTable->export());
        }

        return $this->render('column/template.html.twig', [
            'employees' => $dataTable->createView(),
            'source_code_classes' => [
                ColumnTemplateDataTableType::class,
            ],
        ]);
    }

    #[Route('/html', name: 'app_column_html')]
    public function html(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $queryBuilder = $employeeRepository->createQueryBuilder('employee');

        $dataTable = $this->createDataTable(ColumnHtmlDataTableType::class, $queryBuilder, options: [
            'themes' => [
                DataTableTheme::from($request->getSession()->get('_data_table_theme'))->getPath(),
                DataTableIconTheme::from($request->getSession()->get('_data_table_icon_theme'))->getPath(),
            ],
        ]);
        $dataTable->handleRequest($request);

        if ($dataTable->isExporting()) {
            return $this->file($dataTable->export());
        }

        return $this->render('column/html.html.twig', [
            'employees' => $dataTable->createView(),
            'source_code_classes' => [
                ColumnHtmlDataTableType::class,
                EmployeeStatus::class,
            ],
        ]);
    }

    #[Route('/icon', name: 'app_column_icon')]
    public function icon(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $queryBuilder = $employeeRepository->createQueryBuilder('employee');

        $dataTable = $this->createDataTable(ColumnIconDataTableType::class, $queryBuilder, options: [
            'themes' => [
                DataTableTheme::from($request->getSession()->get('_data_table_theme'))->getPath(),
                DataTableIconTheme::from($request->getSession()->get('_data_table_icon_theme'))->getPath(),
            ],
        ]);
        $dataTable->handleRequest($request);

        if ($dataTable->isExporting()) {
            return $this->file($dataTable->export());
        }

        return $this->render('column/icon.html.twig', [
            'employees' => $dataTable->createView(),
            'source_code_classes' => [
                ColumnIconDataTableType::class,
                EmployeeStatus::class,
            ],
        ]);
    }
}
