<?php

declare(strict_types=1);

namespace App\DataTable\Type\Filter;

use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\OdsExporterType;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\XlsxExporterType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\SearchFilterType;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class FilterSearchAdvancedDataTableType extends AbstractDataTableType
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
            ])
            ->addColumn('lastName', TextColumnType::class, [
                'export' => true,
                'label' => 'employee.lastName',
            ])
            ->addColumn('title', TextColumnType::class, [
                'export' => true,
                'label' => 'contract.title',
                'property_path' => 'currentContract?.title.name',
            ])
            ->addFilter(DataTableBuilderInterface::SEARCH_FILTER_NAME, SearchFilterType::class, [
                'handler' => function (ProxyQueryInterface $query, string $search) {
                    $query
                        ->andWhere('employee.firstName LIKE :search OR employee.lastName LIKE :search OR currentContractTitle.name LIKE :search')
                        ->setParameter('search', '%'.$search.'%')
                    ;
                },
                'form_options' => [
                    'translation_domain' => 'KreyuDataTable',
                ],
                'translation_domain' => false,
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
