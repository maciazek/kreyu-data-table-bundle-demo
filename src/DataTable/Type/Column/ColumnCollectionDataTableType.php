<?php

declare(strict_types=1);

namespace App\DataTable\Type\Column;

use App\Entity\Contract;
use App\Entity\Employee;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\OdsExporterType;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\XlsxExporterType;
use Kreyu\Bundle\DataTableBundle\Column\Type\CollectionColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\LinkColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatableMessage;

class ColumnCollectionDataTableType extends AbstractDataTableType
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addColumn('name', TextColumnType::class, [
                'export' => true,
                'getter' => fn (Employee $employee) => $employee->getFirstName().' '.$employee->getLastName(),
            ])
            ->addColumn('entities', CollectionColumnType::class, [
                'export' => true,
                'entry_options' => [
                    'formatter' => fn (Contract $contract) => $contract->getTitle()->getName(),
                ],
                'property_path' => 'contracts',
            ])
            ->addColumn('enums', CollectionColumnType::class, [
                'export' => true,
                'property_path' => 'roles',
                'sort' => 'roles',
            ])
            ->addColumn('links', CollectionColumnType::class, [
                'entry_type' => LinkColumnType::class,
                'entry_options' => [
                    'formatter' => fn (Contract $contract) => $contract->getTitle()->getName(),
                    'href' => fn (Contract $contract) => $this->urlGenerator->generate('app_contract_show', [
                        'id' => $contract->getId(),
                    ]),
                ],
                'export' => true,
                'property_path' => 'contracts',
            ])
            ->addColumn('customSeparator', CollectionColumnType::class, [
                'export' => true,
                'property_path' => 'roles',
                'separator' => ' ➕ ',
                'sort' => 'roles',
            ])
            ->addColumn('customHtmlSeparator', CollectionColumnType::class, [
                'export' => true,
                'property_path' => 'roles',
                'separator' => '<br>',
                'separator_html' => true,
                'sort' => 'roles',
            ])
            ->addColumn('customTranslatableSeparator', CollectionColumnType::class, [
                'export' => true,
                'property_path' => 'roles',
                'separator' => new TranslatableMessage('and', [], 'messages'),
                'sort' => 'roles',
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
