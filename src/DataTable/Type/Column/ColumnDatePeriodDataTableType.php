<?php

declare(strict_types=1);

namespace App\DataTable\Type\Column;

use App\Entity\Employee;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\OdsExporterType;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\XlsxExporterType;
use Kreyu\Bundle\DataTableBundle\Column\Type\DatePeriodColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ColumnDatePeriodDataTableType extends AbstractDataTableType
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
            ->addColumn('basic', DatePeriodColumnType::class, [
                'export' => true,
                'getter' => fn (Employee $employee) => new \DatePeriod($employee->getCurrentContract()->getBeginDate(), new \DateInterval('P1D'), $employee->getCurrentContract()->getEndDate() ?? new \DateTime()),
            ])
            ->addColumn('customFormat', DatePeriodColumnType::class, [
                'export' => true,
                'format' => 'm/d/y H:i',
                'getter' => fn (Employee $employee) => new \DatePeriod($employee->getCurrentContract()->getBeginDate(), new \DateInterval('P1D'), $employee->getCurrentContract()->getEndDate() ?? new \DateTime()),
            ])
            ->addColumn('customTimezone', DatePeriodColumnType::class, [
                'export' => true,
                'getter' => fn (Employee $employee) => new \DatePeriod($employee->getCurrentContract()->getBeginDate(), new \DateInterval('P1D'), $employee->getCurrentContract()->getEndDate() ?? new \DateTime()),
                'timezone' => 'Pacific/Kiritimati',
            ])
            ->addColumn('customSeparator', DatePeriodColumnType::class, [
                'export' => true,
                'getter' => fn (Employee $employee) => new \DatePeriod($employee->getCurrentContract()->getBeginDate(), new \DateInterval('P1D'), $employee->getCurrentContract()->getEndDate() ?? new \DateTime()),
                'separator' => ' — ',
            ])
            ->addColumn('customAll', DatePeriodColumnType::class, [
                'export' => true,
                'format' => 'm/d/y H:i',
                'getter' => fn (Employee $employee) => new \DatePeriod($employee->getCurrentContract()->getBeginDate(), new \DateInterval('P1D'), $employee->getCurrentContract()->getEndDate() ?? new \DateTime()),
                'separator' => ' — ',
                'timezone' => 'Pacific/Kiritimati',
            ])
            // ->addColumn('customFormat', DateTimeColumnType::class, [
            //     'export' => true,
            //     'format' => 'm/d/y H:i',
            //     'property_path' => 'lastLoginAt',
            //     'sort' => 'lastLoginAt',
            // ])
            // ->addColumn('differentTimezone', DateTimeColumnType::class, [
            //     'export' => true,
            //     'property_path' => 'lastLoginAt',
            //     'sort' => 'lastLoginAt',
            //     'timezone' => 'Pacific/Kiritimati',
            // ])
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
