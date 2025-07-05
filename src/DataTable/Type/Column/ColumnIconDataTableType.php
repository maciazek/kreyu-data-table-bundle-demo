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
            ->addColumn('boolean', IconColumnType::class, [
                'export' => true,
                'icon' => fn (?bool $isManager) => $isManager ? 'star' : null,
                'property_path' => 'isManager',
                'sort' => 'isManager',
            ])
            ->addColumn('isActive', IconColumnType::class, [
                'export' => [
                    'label' => 'Is active',
                ],
                'getter' => fn (Employee $employee) => $employee->getStatus() === EmployeeStatus::ACT ? $employee->getStatus() : null,
                'header_attr' => [
                    'class' => 'w-0 abbr text-center',
                    'data-bootstrap-target' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'data-bs-title' => $this->translator->trans('App\\Enum\\EmployeeStatus::ACT', [], 'enums').'?',
                ],
                'icon' => fn (?EmployeeStatus $status) => $status?->getIcon(DataTableIconTheme::from($this->requestStack->getSession()->get('_data_table_icon_theme'))),
                'icon_attr' => fn (?EmployeeStatus $status) => [
                    'class' => 'text-'.$status?->getContext(),
                ],
                'label' => mb_substr($this->translator->trans('App\\Enum\\EmployeeStatus::ACT', [], 'enums'), 0, 1),
                'property_path' => 'status',
            ])
            ->addColumn('isInactive', IconColumnType::class, [
                'export' => [
                    'label' => 'Is inactive',
                ],
                'getter' => fn (Employee $employee) => $employee->getStatus() === EmployeeStatus::INA ? $employee->getStatus() : null,
                'header_attr' => [
                    'class' => 'w-0 abbr text-center',
                    'data-bootstrap-target' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'data-bs-title' => $this->translator->trans('App\\Enum\\EmployeeStatus::INA', [], 'enums').'?',
                ],
                'icon' => fn (?EmployeeStatus $status) => $status?->getIcon(DataTableIconTheme::from($this->requestStack->getSession()->get('_data_table_icon_theme'))),
                'icon_attr' => fn (?EmployeeStatus $status) => [
                    'class' => 'text-'.$status?->getContext(),
                ],
                'label' => mb_substr($this->translator->trans('App\\Enum\\EmployeeStatus::INA', [], 'enums'), 0, 1),
                'property_path' => 'status',
            ])
            ->addColumn('isLongTermLeave', IconColumnType::class, [
                'export' => [
                    'label' => 'Is long term leave',
                ],
                'getter' => fn (Employee $employee) => $employee->getStatus() === EmployeeStatus::LTL ? $employee->getStatus() : null,
                'header_attr' => [
                    'class' => 'w-0 abbr text-center',
                    'data-bootstrap-target' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'data-bs-title' => $this->translator->trans('App\\Enum\\EmployeeStatus::LTL', [], 'enums').'?',
                ],
                'icon' => fn (?EmployeeStatus $status) => $status?->getIcon(DataTableIconTheme::from($this->requestStack->getSession()->get('_data_table_icon_theme'))),
                'icon_attr' => fn (?EmployeeStatus $status) => [
                    'class' => 'text-'.$status?->getContext(),
                ],
                'label' => mb_substr($this->translator->trans('App\\Enum\\EmployeeStatus::LTL', [], 'enums'), 0, 1),
                'property_path' => 'status',
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
