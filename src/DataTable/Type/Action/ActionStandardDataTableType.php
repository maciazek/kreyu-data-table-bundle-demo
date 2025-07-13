<?php

declare(strict_types=1);

namespace App\DataTable\Type\Action;

use App\DataTable\Filter\Formatter\DateRangeActiveFilterFormatter;
use App\Entity\Employee;
use App\Enum\EmployeeStatus;
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\Dropdown\DropdownActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\Dropdown\LinkDropdownItemActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\FormActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\LinkActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\ModalActionType;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\OdsExporterType;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\XlsxExporterType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ActionsColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\EnumColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ActionStandardDataTableType extends AbstractDataTableType
{
    public function __construct(
        private TranslatorInterface $translator,
        private UrlGeneratorInterface $urlGenerator,
        private DateRangeActiveFilterFormatter $dateRangeActiveFilterFormatter,
    ) {
    }

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addAction('new', ButtonActionType::class, [
                'href' => $this->urlGenerator->generate('app_employee_new'),
                'icon' => 'plus-lg',
                'label' => 'app_employee_new',
                'translation_domain' => 'routes',
                'variant' => 'success',
            ])
            ->addColumn('link', ActionsColumnType::class, [
                'actions' => [
                    'show' => $builder->addRowAction('show', LinkActionType::class, [
                        'href' => fn (Employee $employee) => $this->urlGenerator->generate('app_employee_show', [
                            'id' => $employee->getId(),
                        ]),
                        'icon' => 'eye',
                        'label' => 'app_employee_show',
                        'translation_domain' => 'routes',
                    ])->getRowAction('show'),
                ],
                'label' => 'Link',
            ])
            ->addColumn('button', ActionsColumnType::class, [
                'actions' => [
                    'edit' => $builder->addRowAction('edit', ButtonActionType::class, [
                        'href' => fn (Employee $employee) => $this->urlGenerator->generate('app_employee_edit', [
                            'id' => $employee->getId(),
                        ]),
                        'icon' => 'pencil',
                        'label' => 'app_employee_edit',
                        'translation_domain' => 'routes',
                        'variant' => 'warning',
                    ])->getRowAction('edit'),
                ],
                'label' => 'Button',
            ])
            ->addColumn('form', ActionsColumnType::class, [
                'actions' => [
                    'activate' => $builder->addRowAction('activate', FormActionType::class, [
                        'action' => fn (Employee $employee) => $this->urlGenerator->generate('app_employee_activate', [
                            'id' => $employee->getId(),
                        ]),
                        'method' => 'POST',
                        'icon' => 'arrow-bar-up',
                        'label' => 'app_employee_activate',
                        'translation_domain' => 'routes',
                        'variant' => 'success',
                        'visible' => fn (Employee $employee) => $employee->getStatus() !== EmployeeStatus::ACTIVE,
                    ])->getRowAction('activate'),
                    'deactivate' => $builder->addRowAction('deactivate', FormActionType::class, [
                        'action' => fn (Employee $employee) => $this->urlGenerator->generate('app_employee_deactivate', [
                            'id' => $employee->getId(),
                        ]),
                        'confirmation' => true,
                        'method' => 'POST',
                        'icon' => 'arrow-bar-down',
                        'label' => 'app_employee_deactivate',
                        'translation_domain' => 'routes',
                        'variant' => 'danger',
                        'visible' => fn (Employee $employee) => $employee->getStatus() === EmployeeStatus::ACTIVE,
                    ])->getRowAction('deactivate'),
                ],
                'label' => 'Form',
            ])
            ->addColumn('modal', ActionsColumnType::class, [
                'actions' => [
                    'delete' => $builder->addRowAction('delete', ModalActionType::class, [
                        'href' => fn (Employee $employee) => $this->urlGenerator->generate('app_employee_delete', [
                            'id' => $employee->getId(),
                        ]),
                        'icon' => 'trash',
                        'label' => 'app_employee_delete',
                        'translation_domain' => 'routes',
                        'variant' => 'destructive',
                    ])->getRowAction('delete'),
                ],
                'label' => 'Modal',
            ])
            ->addColumn('dropdown', ActionsColumnType::class, [
                'actions' => [
                    'dropdown' => $builder->addRowAction('dropdown', DropdownActionType::class, [
                        'actions' => [
                            $builder->createAction('show', LinkDropdownItemActionType::class, [
                                'href' => fn (Employee $employee) => $this->urlGenerator->generate('app_employee_show', [
                                    'id' => $employee->getId(),
                                ]),
                                'icon' => 'eye',
                                'label' => 'app_employee_show',
                                'translation_domain' => 'routes',
                            ]),
                            $builder->createAction('edit', LinkDropdownItemActionType::class, [
                                'href' => fn (Employee $employee) => $this->urlGenerator->generate('app_employee_edit', [
                                    'id' => $employee->getId(),
                                ]),
                                'icon' => 'pencil',
                                'label' => 'app_employee_edit',
                                'translation_domain' => 'routes',
                            ]),
                        ],
                        'icon' => 'list',
                        'label' => 'Options',
                        'variant' => 'secondary',
                    ])->getRowAction('dropdown'),
                ],
                'label' => 'Dropdown',
            ])
            ->addColumn('name', TextColumnType::class, [
                'export' => true,
                'getter' => fn (Employee $employee) => $employee->getFirstName().' '.$employee->getLastName(),
            ])
            ->addColumn('status', EnumColumnType::class, [
                'export' => true,
                'value_attr' => function (EmployeeStatus $status) {
                    return [
                        'class' => 'badge fw-normal text-bg-'.$status->getContext(),
                    ];
                },
            ])
            ->setAutoAddingActionsColumn(false)
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
