<?php

declare(strict_types=1);

namespace App\DataTable\Type\Column;

use App\Entity\Contract;
use App\Entity\Employee;
use App\Enum\EmployeeRole;
use Doctrine\ORM\PersistentCollection;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\OdsExporterType;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\XlsxExporterType;
use Kreyu\Bundle\DataTableBundle\Column\Type\CollectionColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\LinkColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ColumnCollectionDataTableType extends AbstractDataTableType
{
    public function __construct(
        private TranslatorInterface $translator,
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
                'formatter' => function (PersistentCollection $contracts): array { // workaround for use with entities
                    return array_map(function (Contract $contract): string {
                        return $contract->getTitle()->getName();
                    }, $contracts->getValues());
                },
                'property_path' => 'contracts',
            ])
            ->addColumn('enums', CollectionColumnType::class, [
                'export' => true,
                'formatter' => function (array $roles): array { // workaround for use with enums
                    return array_map(function (EmployeeRole $role): string {
                        return $role->trans($this->translator);
                    }, $roles);
                },
                'property_path' => 'roles',
                'sort' => 'roles',
            ])
            ->addColumn('links', CollectionColumnType::class, [
                'entry_type' => LinkColumnType::class,
                'entry_options' => [
                    'formatter' => function (Contract $contract): string {
                        return $contract->getTitle()->getName();
                    },
                    'getter' => fn (Contract $contract) => $contract, // workaround for use with entities
                    'href' => function ($contract): string {
                        return '#fakeroute'.$contract->getId();
                    },
                ],
                'export' => true,
                'property_path' => 'contracts',
            ])
            ->addColumn('customSeparator', CollectionColumnType::class, [
                'export' => true,
                'formatter' => function (array $roles): array { // workaround for use with enums
                    return array_map(function (EmployeeRole $role): string {
                        return $role->trans($this->translator);
                    }, $roles);
                },
                'property_path' => 'roles',
                'separator' => '➕',
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
