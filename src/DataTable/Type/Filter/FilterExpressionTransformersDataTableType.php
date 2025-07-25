<?php

declare(strict_types=1);

namespace App\DataTable\Type\Filter;

use App\Entity\Employee;
use App\Enum\EmployeeRole;
use Doctrine\ORM\Query\Expr;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ExpressionTransformer\CallbackExpressionTransformer;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\DoctrineOrmFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\OdsExporterType;
use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type\XlsxExporterType;
use Kreyu\Bundle\DataTableBundle\Column\Type\CollectionColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\EnumColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class FilterExpressionTransformersDataTableType extends AbstractDataTableType
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
            ->addColumn('roles', CollectionColumnType::class, [
                'entry_type' => EnumColumnType::class,
                'export' => true,
                'label' => 'employee.roles',
                'property_path' => 'roles',
                'sort' => 'roles',
            ])
            ->addFilter('roles', DoctrineOrmFilterType::class, [
                'active_filter_formatter' => fn (FilterData $data) => EmployeeRole::from($data->getValue()),
                'expression_transformers' => [
                    new CallbackExpressionTransformer(function (mixed $expression) {
                        // to use JSON functions, install "scienta/doctrine-json-functions"
                        // https://github.com/ScientaNL/DoctrineJsonFunctions

                        // SQLite:
                        return new Expr\Comparison(
                            $expression->getLeftExpr(),
                            'LIKE',
                            new Expr\Func(
                                'CONCAT',
                                ['\'%\'', new Expr\Func('JSON_QUOTE', $expression->getRightExpr()), '\'%\''],
                            ),
                        );

                        // MySQL/MariaDB:
                        // return new Expr\Comparison(
                        //     new Expr\Func(
                        //         'JSON_CONTAINS',
                        //         [$expression->getLeftExpr(), new Expr\Func('JSON_QUOTE', [$expression->getRightExpr()])],
                        //     ),
                        //     $expression->getOperator(),
                        //     true,
                        // );
                    }),
                ],
                'form_options' => [
                    'choices' => array_column(EmployeeRole::cases(), 'value'),
                    'choice_label' => fn ($choice) => EmployeeRole::from($choice),
                ],
                'form_type' => ChoiceType::class,
                'label' => 'employee.roles',
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
