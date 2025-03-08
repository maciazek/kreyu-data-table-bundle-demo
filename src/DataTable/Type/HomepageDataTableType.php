<?php

declare(strict_types=1);

namespace App\DataTable\Type;

use App\DataTable\Filter\Formatter\DateRangeActiveFilterFormatter;
use App\Enum\EmployeeStatus;
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\DateRangeFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\DoctrineOrmFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\StringFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\OdsExporterType;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\XlsxExporterType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ActionsColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\DateTimeColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\EnumColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\MoneyColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\Type\SearchFilterType;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class HomepageDataTableType extends AbstractDataTableType
{
    public function __construct(
        private TranslatorInterface $translator,
        private DateRangeActiveFilterFormatter $dateRangeActiveFilterFormatter,
    ) {
    }

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addColumn('actions', ActionsColumnType::class, [
                'actions' => [
                    'show' => [
                        'type' => ButtonActionType::class,
                        'type_options' => [
                            'attr' => [
                                'class' => 'btn btn-sm btn-info',
                                'data-bootstrap-target' => 'tooltip',
                                'data-bs-placement' => 'left',
                                'data-bs-title' => $this->translator->trans('show', [], 'buttons'),
                            ],
                            'href' => '#',
                            'icon' => 'eye',
                            'label' => '',
                        ],
                    ],
                    'edit' => [
                        'type' => ButtonActionType::class,
                        'type_options' => [
                            'attr' => [
                                'class' => 'btn btn-sm btn-warning',
                                'data-bootstrap-target' => 'tooltip',
                                'data-bs-placement' => 'right',
                                'data-bs-title' => $this->translator->trans('edit', [], 'buttons'),
                            ],
                            'href' => '#',
                            'icon' => 'pencil',
                            'label' => '',
                        ],
                    ],
                ],
                'header_attr' => [
                    'class' => 'w-0',
                ],
                'label' => '',
                'priority' => 0,
                'value_attr' => [
                    'class' => 'text-nowrap small',
                ],
            ])
            ->addColumn('firstName', TextColumnType::class, [
                'export' => true,
                'label' => 'employee.firstName',
                'sort' => true,
            ])
            ->addColumn('lastName', TextColumnType::class, [
                'export' => true,
                'label' => 'employee.lastName',
                'sort' => true,
            ])
            ->addColumn('birthDate', DateTimeColumnType::class, [
                'export' => true,
                'format' => $this->translator->trans('date_format', [], 'messages'),
                'label' => 'employee.birthDate',
                'sort' => true,
            ])
            ->addColumn('title', TextColumnType::class, [
                'export' => true,
                'label' => 'contract.title',
                'property_path' => 'currentContract?.title.name',
                'sort' => 'currentContractTitle.name',
            ])
            ->addColumn('salary', MoneyColumnType::class, [
                'currency' => $this->translator->trans('currency', [], 'messages'),
                'export' => true,
                'label' => 'contract.salary',
                'property_path' => 'currentContract?.salary',
                'sort' => 'currentContract.salary',
            ])
            ->addColumn('status', EnumColumnType::class, [
                'export' => true,
                'label' => 'employee.status',
                'sort' => 'status',
                'value_attr' => function (EmployeeStatus $status) {
                    return [
                        'class' => 'badge fw-normal text-wrap text-bg-'.$status->getContext(),
                    ];
                },
            ])
            ->addFilter(DataTableBuilderInterface::SEARCH_FILTER_NAME, SearchFilterType::class, [
                'handler' => function (ProxyQueryInterface $query, string $search) {
                    return $query
                        ->andWhere('employee.firstName LIKE :search OR employee.lastName LIKE :search OR currentContractTitle.name LIKE :search')
                        ->setParameter('search', '%'.$search.'%')
                    ;
                },
                'form_options' => [
                    'translation_domain' => 'KreyuDataTable',
                ],
            ])
            ->addFilter('firstName', StringFilterType::class, [
                'label' => 'employee.firstName',
            ])
            ->addFilter('lastName', StringFilterType::class, [
                'label' => 'employee.lastName',
            ])
            ->addFilter('birthDate', DateRangeFilterType::class, [
                'active_filter_formatter' => $this->dateRangeActiveFilterFormatter,
                'label' => 'employee.birthDate',
            ])
            ->addFilter('status', DoctrineOrmFilterType::class, [
                'active_filter_formatter' => function (FilterData $data) {
                    return EmployeeStatus::from($data->getValue());
                },
                'form_options' => [
                    'choices' => array_column(EmployeeStatus::cases(), 'name'),
                    'choice_label' => function ($choice) {
                        return EmployeeStatus::from($choice);
                    },
                ],
                'form_type' => ChoiceType::class,
                'label' => 'employee.status',
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
        $resolver->setDefaults([
            'personalization_enabled' => true,
            'translation_domain' => 'entities',
        ]);
    }
}
