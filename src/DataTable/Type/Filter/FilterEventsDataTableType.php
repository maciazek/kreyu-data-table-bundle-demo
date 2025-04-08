<?php

declare(strict_types=1);

namespace App\DataTable\Type\Filter;

use App\Entity\City;
use App\Entity\Employee;
use Doctrine\DBAL\ParameterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Event\DoctrineOrmFilterEvents;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Event\PreSetParametersEvent;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\EntityFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\OdsExporterType;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\XlsxExporterType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class FilterEventsDataTableType extends AbstractDataTableType
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
            ->addColumn('address?.city.name', TextColumnType::class, [
                'export' => true,
                'label' => 'address.city',
                'sort' => 'city.name',
            ])
            ->addFilter('city', EntityFilterType::class, [ // city has UUID as primary key
                'form_options' => [
                    'class' => City::class,
                    'choice_label' => 'name',
                ],
                'choice_label' => 'name',
                'label' => 'address.city',
                'query_path' => 'address.city',
            ])
            ->addExporter('ods', OdsExporterType::class)
            ->addExporter('xlsx', XlsxExporterType::class)
            ->setDefaultPaginationData(PaginationData::fromArray([
                'page' => 1,
                'perPage' => 10,
            ]))
        ;

        $builder
            ->getFilter('city') // city has UUID as primary key
            ->addEventListener(DoctrineOrmFilterEvents::PRE_SET_PARAMETERS, function (PreSetParametersEvent $event) {
                $parameter = $event->getParameters()[0];
                $parameter->setValue($parameter->getValue()->getId()->toBinary(), ParameterType::BINARY);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'entities',
        ]);
    }
}
