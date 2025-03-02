<?php

declare(strict_types=1);

namespace App\DataTable\Type\Column;

use App\DataTable\Filter\Formatter\DateRangeActiveFilterFormatter;
use App\Entity\Employee;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\OdsExporterType;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\XlsxExporterType;
use Kreyu\Bundle\DataTableBundle\Column\Type\MoneyColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ColumnMoneyDataTableType extends AbstractDataTableType
{
    public function __construct(
        private TranslatorInterface $translator,
        private DateRangeActiveFilterFormatter $dateRangeActiveFilterFormatter,
    ) {
    }

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addColumn('name', TextColumnType::class, [
                'export' => true,
                'getter' => fn (Employee $employee) => $employee->getFirstName().' '.$employee->getLastName(),
            ])
            ->addColumn('basic', MoneyColumnType::class, [
                'currency' => 'USD',
                'export' => true,
                'property_path' => 'currentContract?.salary',
                'sort' => 'currentContract.salary',
            ])
            ->addColumn('inCents', MoneyColumnType::class, [
                'currency' => 'USD',
                'divisor' => 100,
                'export' => true,
                'property_path' => 'currentContract?.salaryInCents',
                'sort' => 'currentContract.salaryInCents',
            ])
            ->addColumn('otherCurrency', MoneyColumnType::class, [
                'currency' => 'PLN',
                'export' => true,
                'property_path' => 'currentContract?.salary',
                'sort' => 'currentContract.salary',
            ])
            ->addColumn('otherCurrencyInCents', MoneyColumnType::class, [
                'currency' => 'PLN',
                'divisor' => 100,
                'export' => true,
                'property_path' => 'currentContract?.salaryInCents',
                'sort' => 'currentContract.salaryInCents',
            ])
            ->addColumn('noIntlFormatter', MoneyColumnType::class, [
                'currency' => 'PLN',
                'export' => true,
                'property_path' => 'currentContract?.salary',
                'sort' => 'currentContract.salary',
                'use_intl_formatter' => false,
            ])
            ->addColumn('noIntlFormatterInCents', MoneyColumnType::class, [
                'currency' => 'PLN',
                'divisor' => 100,
                'export' => true,
                'property_path' => 'currentContract?.salaryInCents',
                'sort' => 'currentContract.salaryInCents',
                'use_intl_formatter' => false,
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
