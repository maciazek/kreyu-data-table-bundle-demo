<?php

declare(strict_types=1);

namespace App\DataTable\Type\Column;

use App\Entity\Employee;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\OdsExporterType;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\XlsxExporterType;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ColumnNumberDataTableType extends AbstractDataTableType
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
            ->addColumn('integer', NumberColumnType::class, [
                'export' => true,
                'property_path' => 'currentContract?.salaryInCents',
                'sort' => 'currentContract.salaryInCents',
            ])
            ->addColumn('decimal', NumberColumnType::class, [
                'export' => true,
                'property_path' => 'currentContract?.salary',
                'sort' => 'currentContract.salary',
            ])
            ->addColumn('percent', NumberColumnType::class, [
                'export' => true,
                'intl_formatter_options' => [
                    'style' => 'percent',
                ],
                'property_path' => 'currentContract?.currentTarget.valueDecimal',
                'sort' => 'currentTarget.valueDecimal',
            ])
            ->addColumn('integerNoIntlFormatter', NumberColumnType::class, [
                'export' => true,
                'property_path' => 'currentContract?.salaryInCents',
                'sort' => 'currentContract.salaryInCents',
                'use_intl_formatter' => false,
            ])
            ->addColumn('decimalNoIntlFormatter', NumberColumnType::class, [
                'export' => true,
                'property_path' => 'currentContract?.salary',
                'sort' => 'currentContract.salary',
                'use_intl_formatter' => false,
            ])
            ->addColumn('decimalCustomIntlFormatterOptions', NumberColumnType::class, [
                'export' => true,
                'intl_formatter_options' => [
                    'attrs' => [
                        'grouping_used' => false,
                        'min_fraction_digit' => 6,
                        'min_integer_digit' => 6,
                    ],
                ],
                'property_path' => 'currentContract?.salary',
                'sort' => 'currentContract.salary',
            ])
            ->addColumn('decimalSpellout', NumberColumnType::class, [
                'export' => true,
                'intl_formatter_options' => [
                    'style' => 'spellout',
                ],
                'property_path' => 'currentContract?.salary',
                'sort' => 'currentContract.salary',
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
