<?php

declare(strict_types=1);

namespace App\DataTable\Type\Column;

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
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;

class ColumnBasicOptionsDataTableType extends AbstractDataTableType
{
    public function __construct(
        private TranslatorInterface $translator,
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
                'export' => true,
                'property_path' => 'firstName',
                'sort' => 'firstName',
            ])
            ->addColumn('labels', TextColumnType::class, [
                'export' => true,
                'label' => 'Label',
                'property_path' => 'lastName',
                'export' => [
                    'label' => 'Different label in export',
                ],
                'sort' => 'lastName',
            ])
            ->addColumn('attrs', TextColumnType::class, [
                'export' => true,
                'property_path' => 'firstName',
                'header_attr' => [
                    'class' => 'text-success',
                ],
                'sort' => 'firstName',
                'value_attr' => fn (string $value, Employee $employee) => [
                    'class' => $employee->getStatus() === EmployeeStatus::INACTIVE ? 'text-danger' : '',
                ],
            ])
            ->addColumn('formatter', TextColumnType::class, [
                'export' => true,
                'property_path' => 'lastName',
                'formatter' => function (string $value, Employee $employee) {
                    return $employee->getStatus() === EmployeeStatus::INACTIVE ? $value.' ❌' : $value;
                },
                'sort' => 'lastName',
            ])
            ->addColumn('getter', TextColumnType::class, [
                'export' => true,
                'getter' => fn (Employee $employee) => $employee->getFirstName().' '.$employee->getLastName(),
            ])
            ->addColumn('translation', TextColumnType::class, [
                'export' => true,
                'label' => new TranslatableMessage('translation', [], 'messages'),
                'getter' => fn (Employee $employee) => count($employee->getRoles() ?? []),
                'value_translation_domain' => 'entities',
                'value_translation_key' => 'employee.rolesCount',
                'value_translation_parameters' => fn (int $count) => [
                    'count' => $count,
                ],
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
