<?php

declare(strict_types=1);

namespace App\DataTable\Type\Column;

use App\Entity\Employee;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\OdsExporterType;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\XlsxExporterType;
use Kreyu\Bundle\DataTableBundle\Column\Type\LinkColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ColumnLinkDataTableType extends AbstractDataTableType
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
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
            ->addColumn('basic', LinkColumnType::class, [
                'export' => true,
                'href' => fn (?string $value) => $value,
                'property_path' => 'website',
            ])
            ->addColumn('route', LinkColumnType::class, [
                'export' => true,
                'getter' => fn (Employee $employee) => $employee->getFirstName().' '.$employee->getLastName(),
                'href' => fn (string $value, Employee $employee) => $this->urlGenerator->generate('app_employee_show', [
                    'id' => $employee->getId(),
                ]),
            ])
            ->addColumn('formatter', LinkColumnType::class, [
                'export' => true,
                'formatter' => fn () => '🔗 '.$this->translator->trans('click_here', [], 'buttons'),
                'href' => fn (?string $value) => $value,
                'property_path' => 'website',
                'sort' => 'website',
                'target' => '_blank',
                'value_attr' => [
                    'class' => 'text-decoration-none',
                ],
            ])
            ->addColumn('button', LinkColumnType::class, [
                'export' => true,
                'formatter' => fn () => 'click_here',
                'header_attr' => [
                    'class' => 'w-0',
                ],
                'href' => fn (?string $value) => $value,
                'property_path' => 'website',
                'sort' => 'website',
                'target' => '_blank',
                'value_attr' => fn (?string $value) => [
                    'class' => $value ? 'btn btn-sm btn-primary text-nowrap' : '',
                ],
                'value_translation_domain' => 'buttons',
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
