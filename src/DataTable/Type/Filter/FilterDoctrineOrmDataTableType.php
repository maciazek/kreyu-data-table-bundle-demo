<?php

declare(strict_types=1);

namespace App\DataTable\Type\Filter;

use App\DataTable\Filter\Formatter\DateRangeActiveFilterFormatter;
use App\Entity\Title;
use App\Enum\EmployeeStatus;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\BooleanFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\DateRangeFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\DoctrineOrmFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\EntityFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\NumericFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\StringFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\OdsExporterType;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\XlsxExporterType;
use Kreyu\Bundle\DataTableBundle\Column\Type\BooleanColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\CollectionColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\DateTimeColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\EnumColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\MoneyColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class FilterDoctrineOrmDataTableType extends AbstractDataTableType
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
            ->addColumn('salaryInCents', MoneyColumnType::class, [
                'currency' => 'USD',
                'export' => true,
                'label' => 'contract.salaryInCents',
                'property_path' => 'currentContract?.salaryInCents',
                'sort' => 'currentContract.salaryInCents',
                'use_intl_formatter' => false,
            ])
            ->addColumn('title', TextColumnType::class, [
                'export' => true,
                'label' => 'contract.title',
                'property_path' => 'currentContract?.title.name',
                'sort' => 'currentContractTitle.name',
            ])
            ->addColumn('isManager', BooleanColumnType::class, [
                'export' => true,
                'label' => 'employee.isManager',
                'sort' => true,
                'value_attr' => fn (?bool $value) => [
                    'class' => $value === null ? 'd-none' : '',
                ],
            ])
            ->addColumn('roles', CollectionColumnType::class, [
                'entry_type' => EnumColumnType::class,
                'export' => true,
                'label' => 'employee.roles',
                'property_path' => 'roles',
                'sort' => 'roles',
            ])
            ->addColumn('status', EnumColumnType::class, [
                'export' => true,
                'label' => 'employee.status',
                'sort' => 'status',
                'value_attr' => fn (EmployeeStatus $status) => [
                    'class' => 'badge fw-normal text-bg-'.$status->getContext(),
                ],
            ])
            ->addFilter('firstName', StringFilterType::class, [
                'label' => 'employee.firstName',
            ])
            ->addFilter('lastName', StringFilterType::class, [
                'label' => 'employee.lastName',
            ])
            ->addFilter('name', StringFilterType::class, [
                'label' => 'employee.name',
                'query_path' => 'CONCAT(employee.firstName, \' \', employee.lastName)',
            ])
            ->addFilter('birthDate', DateRangeFilterType::class, [
                'active_filter_formatter' => $this->dateRangeActiveFilterFormatter,
                'label' => 'employee.birthDate',
            ])
            ->addFilter('salaryInCentsFrom', NumericFilterType::class, [
                'default_operator' => Operator::GreaterThanEquals,
                'label' => 'contract.salaryInCentsFrom',
                'query_path' => 'currentContract.salaryInCents',
            ])
            ->addFilter('salaryInCentsTo', NumericFilterType::class, [
                'default_operator' => Operator::LessThanEquals,
                'label' => 'contract.salaryInCentsTo',
                'query_path' => 'currentContract.salaryInCents',
            ])
            ->addFilter('title', EntityFilterType::class, [
                'choice_label' => 'name',
                'form_options' => [
                    'class' => Title::class,
                    'choice_label' => 'name',
                ],
                'label' => 'contract.title',
                'query_path' => 'currentContract.title',
            ])
            ->addFilter('isManager', BooleanFilterType::class, [
                'label' => 'employee.isManager',
            ])
            ->addFilter('numberOfRoles', NumericFilterType::class, [
                'form_type' => IntegerType::class,
                'label' => 'employee.numberOfRoles',

                // to use JSON functions, install "scienta/doctrine-json-functions"
                // https://github.com/ScientaNL/DoctrineJsonFunctions
                'query_path' => 'JSON_ARRAY_LENGTH(employee.roles)', // SQLite
                // 'query_path' => 'JSON_LENGTH(employee.roles)', // MySQL/MariaDB
            ])
            ->addFilter('status', DoctrineOrmFilterType::class, [
                'active_filter_formatter' => function (FilterData $data) {
                    return implode(', ', array_map(function ($item) {
                        return EmployeeStatus::from($item)->trans($this->translator);
                    }, $data->getValue()));
                },
                'default_operator' => Operator::In,
                'form_options' => [
                    'choices' => array_column(EmployeeStatus::cases(), 'value'),
                    'choice_label' => fn ($choice) => EmployeeStatus::from($choice),
                    'expanded' => true,
                    'multiple' => true,
                ],
                'form_type' => ChoiceType::class,
                'label' => 'employee.status',
            ])
            ->addExporter('ods', OdsExporterType::class)
            ->addExporter('xlsx', XlsxExporterType::class)
            ->setDefaultFiltrationData(FiltrationData::fromArray([
                'salaryInCentsFrom' => new FilterData(value: 1000000),
                'isManager' => new FilterData(value: false),
                'status' => new FilterData(value: [EmployeeStatus::ACTIVE->value, EmployeeStatus::LONG_TERM_LEAVE->value]),
            ]))
            ->setDefaultPaginationData(PaginationData::fromArray([
                'page' => 1,
                'perPage' => 10,
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
