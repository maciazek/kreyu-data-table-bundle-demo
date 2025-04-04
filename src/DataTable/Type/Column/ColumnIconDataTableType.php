<?php

declare(strict_types=1);

namespace App\DataTable\Type\Column;

use App\Entity\Employee;
use App\Enum\DataTableIconTheme;
use App\Enum\EmployeeStatus;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\OdsExporterType;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\XlsxExporterType;
use Kreyu\Bundle\DataTableBundle\Column\Type\IconColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ColumnIconDataTableType extends AbstractDataTableType
{
    public function __construct(
        private TranslatorInterface $translator,
        private RequestStack $requestStack,
    ) {
    }

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addColumn('name', TextColumnType::class, [
                'export' => true,
                'getter' => fn (Employee $employee) => $employee->getFirstName().' '.$employee->getLastName(),
                'value_attr' => fn (string $value, Employee $employee) => [
                    'class' => 'badge fw-normal text-bg-'.$employee->getStatus()->getContext(),
                ],
            ])
            ->addColumn('basic', IconColumnType::class, [
                'icon' => 'clock',
                'property_path' => false,
            ])
            ->addColumn('customAttrs', IconColumnType::class, [
                'icon' => 'sun',
                'icon_attr' => [
                    'class' => 'text-danger',
                ],
                'property_path' => false,
            ])
            ->addColumn('dynamic', IconColumnType::class, [
                'export' => true,
                'icon' => fn (EmployeeStatus $status) => $status->getIcon(DataTableIconTheme::from($this->requestStack->getSession()->get('_data_table_icon_theme'))),
                'icon_attr' => fn (EmployeeStatus $status) => [
                    'class' => 'text-'.$status->getContext(),
                ],
                'property_path' => 'status',
                'sort' => 'status',
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
