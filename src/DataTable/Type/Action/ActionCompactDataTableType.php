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
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class ActionCompactDataTableType extends AbstractDataTableType
{
    public function __construct(
        private TranslatorInterface $translator,
        private UrlGeneratorInterface $urlGenerator,
        private DateRangeActiveFilterFormatter $dateRangeActiveFilterFormatter,
        private CsrfTokenManagerInterface $csrfTokenManager,
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
            ->addRowAction('show', LinkActionType::class, [
                'attr' => [
                    'data-bootstrap-target' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'data-bs-title' => $this->translator->trans('app_employee_show', [], 'routes'),
                ],
                'href' => fn (Employee $employee) => $this->urlGenerator->generate('app_employee_show', [
                    'id' => $employee->getId(),
                ]),
                'icon' => 'eye',
                'label' => '',
            ])
            ->addRowAction('edit', ButtonActionType::class, [
                'attr' => [
                    'data-bootstrap-target' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'data-bs-title' => $this->translator->trans('app_employee_edit', [], 'routes'),
                ],
                'href' => fn (Employee $employee) => $this->urlGenerator->generate('app_employee_edit', [
                    'id' => $employee->getId(),
                ]),
                'icon' => 'pencil',
                'label' => '',
                'variant' => 'warning',
            ])
            ->addRowAction('activate', FormActionType::class, [
                'attr' => [
                    'data-bootstrap-target' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'data-bs-title' => $this->translator->trans('app_employee_activate', [], 'routes'),
                ],
                'action' => fn (Employee $employee) => $this->urlGenerator->generate('app_employee_activate', [
                    'id' => $employee->getId(),
                ]),
                'method' => 'POST',
                'icon' => 'arrow-bar-up',
                'label' => '',
                'variant' => 'success',
                'visible' => fn (Employee $employee) => $employee->getStatus() !== EmployeeStatus::ACT,
            ])
            ->addRowAction('deactivate', FormActionType::class, [
                'action' => fn (Employee $employee) => $this->urlGenerator->generate('app_employee_deactivate', [
                    'id' => $employee->getId(),
                ]),
                'attr' => [
                    'data-bootstrap-target' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'data-bs-title' => $this->translator->trans('app_employee_deactivate', [], 'routes'),
                ],
                'confirmation' => true,
                'method' => 'POST',
                'icon' => 'arrow-bar-down',
                'label' => '',
                'variant' => 'danger',
                'visible' => fn (Employee $employee) => $employee->getStatus() === EmployeeStatus::ACT,
            ])
            ->addRowAction('delete', ModalActionType::class, [
                'attr' => [
                    'data-bootstrap-target' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'data-bs-title' => $this->translator->trans('app_employee_delete', [], 'routes'),
                ],
                'href' => fn (Employee $employee) => $this->urlGenerator->generate('app_employee_delete', [
                    'id' => $employee->getId(),
                ]),
                'icon' => 'trash',
                'label' => '',
                'variant' => 'destructive',
            ])
            ->addRowAction('dropdown', DropdownActionType::class, [
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
                'attr' => [
                    'data-bootstrap-target' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'data-bs-title' => 'Options',
                ],
                'icon' => 'list',
                'label' => '',
                'variant' => 'secondary',
            ])
            ->addColumn('actions', ActionsColumnType::class, [
                'actions' => $builder->getRowActions(),
                'header_attr' => [
                    'class' => 'w-0',
                ],
                'label' => '',
                'value_attr' => [
                    'class' => 'text-nowrap',
                ],
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
