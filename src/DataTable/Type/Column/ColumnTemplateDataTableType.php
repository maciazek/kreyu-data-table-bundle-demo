<?php

declare(strict_types=1);

namespace App\DataTable\Type\Column;

use App\Entity\Employee;
use Doctrine\ORM\PersistentCollection;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\OdsExporterType;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\XlsxExporterType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TemplateColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class ColumnTemplateDataTableType extends AbstractDataTableType
{
    public function __construct(
        private TranslatorInterface $translator,
        private ChartBuilderInterface $chartBuilder,
    ) {
    }

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $translator = $this->translator;
        $chartBuilder = $this->chartBuilder;

        $builder
            ->addColumn('name', TextColumnType::class, [
                'export' => true,
                'getter' => fn (Employee $employee) => $employee->getFirstName().' '.$employee->getLastName(),
            ])
            ->addColumn('progressbar', TemplateColumnType::class, [
                'export' => true,
                'property_path' => 'currentContract?.currentTarget.value',
                'template_path' => 'column/_progressbar.html.twig',
                'template_vars' => function (?int $value) {
                    return [
                        'value' => $value,
                    ];
                },
            ])
            ->addColumn('chart', TemplateColumnType::class, [
                'header_attr' => [
                    'class' => 'w-0',
                ],
                'property_path' => 'currentContract?.targets',
                'template_path' => 'column/_chart.html.twig',
                'template_vars' => function (?PersistentCollection $targets) use ($translator, $chartBuilder) {
                    if ($targets === null) {
                        return [
                            'chart' => null,
                        ];
                    }

                    $values = array_map(function ($target) {
                        return [
                            'month' => $target->getMonth()->format('Y-m'),
                            'backgroundColor' => $target->getValue() == 100 ? 'rgba(25, 135, 84, 0.5)' : 'rgba(13, 110, 253, 0.5)',
                            'borderColor' => $target->getValue() == 100 ? 'rgb(25, 135, 84)' : 'rgb(13, 110, 253)',
                            'value' => $target->getValue(),
                        ];
                    }, $targets->getValues());

                    $chart = $chartBuilder->createChart(Chart::TYPE_BAR);
                    $chart->setData([
                        'labels' => array_column($values, 'month'),
                        'datasets' => [
                            [
                                'label' => $translator->trans('target.value', [], 'entities'),
                                'backgroundColor' => array_column($values, 'backgroundColor'),
                                'borderColor' => array_column($values, 'borderColor'),
                                'data' => array_column($values, 'value'),
                            ],
                        ],
                    ]);
                    $chart->setOptions([
                        'elements' => [
                            'bar' => [
                                'borderWidth' => 2,
                            ],
                        ],
                        'responsive' => false,
                        'aspectRatio' => 7,
                        'plugins' => [
                            'legend' => [
                                'display' => false,
                            ],
                        ],
                        'scales' => [
                            'y' => [
                                'suggestedMin' => 10,
                                'suggestedMax' => 100,
                                'display' => false,
                            ],
                            'x' => [
                                'display' => false,
                            ],
                        ],
                    ]);

                    return [
                        'chart' => $chart,
                    ];
                },
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
