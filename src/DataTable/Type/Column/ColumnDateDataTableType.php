<?php

declare(strict_types=1);

namespace App\DataTable\Type\Column;

use App\Entity\Employee;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\OdsExporterType;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\XlsxExporterType;
use Kreyu\Bundle\DataTableBundle\Column\Type\DateColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ColumnDateDataTableType extends AbstractDataTableType
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addColumn('name', TextColumnType::class, [
                'export' => true,
                'getter' => fn (Employee $employee) => $employee->getFirstName().' '.$employee->getLastName(),
            ])
            ->addColumn('basic', DateColumnType::class, [
                'export' => true,
                'property_path' => 'lastLoginAt',
                'sort' => 'lastLoginAt',
            ])
            ->addColumn('customFormat', DateColumnType::class, [
                'export' => true,
                'format' => 'm/d/y',
                'property_path' => 'lastLoginAt',
                'sort' => 'lastLoginAt',
            ])
            ->addColumn('customTimezone', DateColumnType::class, [
                'export' => true,
                'property_path' => 'lastLoginAt',
                'sort' => 'lastLoginAt',
                'timezone' => 'Pacific/Kiritimati',
            ])
            ->addColumn('customAll', DateColumnType::class, [
                'export' => true,
                'format' => 'm/d/y',
                'property_path' => 'lastLoginAt',
                'sort' => 'lastLoginAt',
                'timezone' => 'Pacific/Kiritimati',
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
