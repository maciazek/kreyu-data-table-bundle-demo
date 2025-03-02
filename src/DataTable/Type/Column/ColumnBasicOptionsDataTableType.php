<?php

declare(strict_types=1);

namespace App\DataTable\Type\Column;

use App\DataTable\Filter\Formatter\DateRangeActiveFilterFormatter;
use App\Entity\Employee;
use App\Enum\EmployeeStatus;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\OdsExporterType;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\XlsxExporterType;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ColumnBasicOptionsDataTableType extends AbstractDataTableType
{
    public function __construct(
        private TranslatorInterface $translator,
        private DateRangeActiveFilterFormatter $dateRangeActiveFilterFormatter,
    ) {
    }

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addColumn('firstName', TextColumnType::class)
            ->addColumn('lastName', TextColumnType::class, [
                'sort' => true,
                'export' => true,
            ])
            ->addColumn('propertyPath', TextColumnType::class, [
                'property_path' => 'firstName',
                'sort' => 'firstName',
            ])
            ->addColumn('exportOptions', TextColumnType::class, [
                'property_path' => 'lastName',
                'export' => [
                    'label' => 'Different export label',
                ],
            ])
            ->addColumn('attrs', TextColumnType::class, [
                'property_path' => 'firstName',
                'header_attr' => [
                    'class' => 'text-success',
                ],
                'value_attr' => function (string $value, Employee $employee) {
                    return [
                        'class' => $employee->getStatus() === EmployeeStatus::INA ? 'text-danger' : '',
                    ];
                },
            ])
            ->addColumn('formatter', TextColumnType::class, [
                'property_path' => 'lastName',
                'formatter' => function (string $value, Employee $employee, ColumnInterface $column, array $options) {
                    return $employee->getStatus() === EmployeeStatus::INA ? $value.' ❌' : $value;
                },
            ])
            ->addColumn('getter', TextColumnType::class, [
                'getter' => function (Employee $employee) {
                    return $employee->getFirstName().' '.$employee->getLastName();
                },
            ])
            ->addColumn('invisible', TextColumnType::class, [
                'visible' => false,
            ])
            ->addExporter('ods', OdsExporterType::class)
            ->addExporter('xlsx', XlsxExporterType::class)
            ->setDefaultPaginationData(PaginationData::fromArray([
                'page' => 1,
                'perPage' => 10,
            ]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    }
}
