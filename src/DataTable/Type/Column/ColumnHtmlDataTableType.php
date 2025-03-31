<?php

declare(strict_types=1);

namespace App\DataTable\Type\Column;

use App\Entity\Employee;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\OdsExporterType;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\XlsxExporterType;
use Kreyu\Bundle\DataTableBundle\Column\Type\HtmlColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ColumnHtmlDataTableType extends AbstractDataTableType
{
    public function __construct(
        private TranslatorInterface $translator,
        private RequestStack $requestStack,
    ) {
    }

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addColumn('name', TextColumnType::class, [
                'export' => true,
                'getter' => fn (Employee $employee) => $employee->getFirstName().' '.$employee->getLastName(),
            ])
            ->addColumn('basic', HtmlColumnType::class, [
                'getter' => fn (Employee $employee) => '<span class="badge fw-normal text-wrap text-bg-'.$employee->getStatus()->getContext().'"><b><i>'.$employee->getStatus()->trans($this->translator).'</i></b></span>',
            ])
            ->addColumn('stripTags', HtmlColumnType::class, [
                'getter' => fn (Employee $employee) => '<span class="badge fw-normal text-wrap text-bg-'.$employee->getStatus()->getContext().'"><b><i>'.$employee->getStatus()->trans($this->translator).'</i></b></span>',
                'strip_tags' => true,
            ])
            ->addColumn('allowedTags', HtmlColumnType::class, [
                'allowed_tags' => ['b', 'i'],
                'getter' => fn (Employee $employee) => '<span class="badge fw-normal text-wrap text-bg-'.$employee->getStatus()->getContext().'"><b><i>'.$employee->getStatus()->trans($this->translator).'</i></b></span>',
                'strip_tags' => true,
            ])
            ->addColumn('raw', HtmlColumnType::class, [
                'getter' => fn (Employee $employee) => '<span class="badge fw-normal text-wrap text-bg-'.$employee->getStatus()->getContext().'"><b><i>'.$employee->getStatus()->trans($this->translator).'</i></b></span>',
                'raw' => false,
                'value_attr' => [
                    'class' => 'font-monospace',
                ],
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
