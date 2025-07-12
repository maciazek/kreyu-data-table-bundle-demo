<?php

declare(strict_types=1);

namespace App\DataTable\Type;

use App\DataTable\Filter\Formatter\DateRangeActiveFilterFormatter;
use App\Entity\Employee;
use App\Enum\EmployeeStatus;
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;
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

class ActionDataTableType extends AbstractDataTableType
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
            ->addColumn('name', TextColumnType::class, [
                'export' => true,
                'getter' => fn (Employee $employee) => $employee->getFirstName().' '.$employee->getLastName(),
            ])
            ->addColumn('link', ActionsColumnType::class, [
                'actions' => [
                    'show' => [
                        'type' => LinkActionType::class,
                        'type_options' => [
                            'href' => fn (Employee $employee) => $this->urlGenerator->generate('app_employee_show', [
                                'id' => $employee->getId(),
                            ]),
                            'icon' => 'eye',
                            'label' => 'app_employee_show',
                            'translation_domain' => 'routes',
                        ],
                    ],
                ],
                'label' => 'Link',
            ])
            ->addColumn('button', ActionsColumnType::class, [
                'actions' => [
                    'edit' => [
                        'type' => ButtonActionType::class,
                        'type_options' => [
                            'href' => fn (Employee $employee) => $this->urlGenerator->generate('app_employee_edit', [
                                'id' => $employee->getId(),
                            ]),
                            'icon' => 'pencil',
                            'label' => 'app_employee_edit',
                            'translation_domain' => 'routes',
                            'variant' => 'warning',
                        ],
                    ],
                ],
                'label' => 'Button',
            ])
            ->addColumn('modal', ActionsColumnType::class, [
                'actions' => [
                    'delete' => [
                        'type' => ModalActionType::class,
                        'type_options' => [
                            'href' => fn (Employee $employee) => $this->urlGenerator->generate('app_employee_delete', [
                                'id' => $employee->getId(),
                            ]),
                            'icon' => 'trash',
                            'label' => 'app_employee_delete',
                            'translation_domain' => 'routes',
                            'variant' => 'destructive',
                        ],
                    ],
                ],
                'label' => 'Modal',
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
