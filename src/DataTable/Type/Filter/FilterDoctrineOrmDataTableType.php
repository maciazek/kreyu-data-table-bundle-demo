<?php

declare(strict_types=1);

namespace App\DataTable\Type\Filter;

use App\Entity\Title;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\BooleanFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\EntityFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\NumericFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\StringFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\OdsExporterType;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\XlsxExporterType;
use Kreyu\Bundle\DataTableBundle\Column\Type\BooleanColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\CollectionColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\EnumColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\MoneyColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class FilterDoctrineOrmDataTableType extends AbstractDataTableType
{
    public function __construct(
        private TranslatorInterface $translator,
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
                'form_options' => [
                    'class' => Title::class,
                    'choice_label' => 'name',
                ],
                'choice_label' => 'name',
                'label' => 'contract.title',
                'query_path' => 'currentContract.title',
            ])
            ->addFilter('isManager', BooleanFilterType::class, [
                'label' => 'employee.isManager',
            ])
            ->addFilter('numberOfRoles', NumericFilterType::class, [
                'label' => 'employee.numberOfRoles',

                // to use JSON functions, install "scienta/doctrine-json-functions"
                // https://github.com/ScientaNL/DoctrineJsonFunctions
                'query_path' => 'JSON_ARRAY_LENGTH(employee.roles)', // SQLite
                // 'query_path' => 'JSON_LENGTH(employee.roles)', // MySQL/MariaDB

                'form_type' => IntegerType::class,
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
            'translation_domain' => 'entities',
        ]);
    }
}
