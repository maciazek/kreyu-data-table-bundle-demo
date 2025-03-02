<?php

declare(strict_types=1);

namespace App\DataTable\Type\Column;

use App\DataTable\Filter\Formatter\DateRangeActiveFilterFormatter;
use App\Entity\Employee;
use Kreyu\Bundle\DataTableBundle\Column\Type\DateTimeColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ColumnDateTimeDataTableType extends AbstractDataTableType
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
                'getter' => fn (Employee $employee) => $employee->getFirstName().' '.$employee->getLastName(),
            ])
            ->addColumn('basic', DateTimeColumnType::class, [
                'property_path' => 'lastLoginAt',
            ])
            ->addColumn('format', DateTimeColumnType::class, [
                'property_path' => 'lastLoginAt',
                'format' => 'm/d/y H:i',
            ])
            ->addColumn('timezone', DateTimeColumnType::class, [
                'property_path' => 'lastLoginAt',
                'timezone' => 'Pacific/Kiritimati',
            ])
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
