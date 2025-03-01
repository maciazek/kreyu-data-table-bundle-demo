<?php

declare(strict_types=1);

namespace App\DataTable\Type;

use App\DataTable\Filter\Formatter\DateRangeActiveFilterFormatter;
use App\Enum\EmployeeStatus;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\DateRangeFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\DoctrineOrmFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\StringFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\OdsExporterType;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\XlsxExporterType;
use Kreyu\Bundle\DataTableBundle\Column\Type\DateTimeColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\EnumColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\MoneyColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
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
            ->addColumn('salary', MoneyColumnType::class, [
                'currency' => $this->translator->trans('currency', [], 'messages'),
                'export' => true,
                'label' => 'contract.salary',
                'property_path' => 'currentContract?.salary',
                'sort' => true,
            ])
            ->addColumn('status', EnumColumnType::class, [
                'export' => true,
                'label' => 'employee.status',
                'value_attr' => function (EmployeeStatus $status) {
                    return [
                        'class' => 'badge fw-normal text-wrap w-min-content text-bg-'.$status->getContext(),
                    ];
                },
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
                'perPage' => 15,
            ]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'entities',
        ]);
    }
}
