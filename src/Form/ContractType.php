<?php

namespace App\Form;

use App\Entity\Contract;
use App\Entity\Employee;
use App\Entity\Target;
use App\Entity\Title;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSetDataEvent;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContractType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', EntityType::class, [
                'class' => Title::class,
                'choice_label' => 'name',
            ])
            ->add('beginDate', null, [
                'widget' => 'single_text',
            ])
            ->add('endDate', null, [
                'widget' => 'single_text',
            ])
            ->add('salaryInCents')
            ->add('salary')
        ;

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (PreSetDataEvent $event): void {
                $form = $event->getForm();
                $contract = $event->getData();

                if ($contract->getId()) {
                    $form->add('currentTarget', EntityType::class, [
                        'class' => Target::class,
                        'choice_label' => fn (Target $target) => $target->getMonth()->format('Y-m'),
                        'query_builder' => function (EntityRepository $er) use ($contract): QueryBuilder {
                            return $er->createQueryBuilder('t')
                                ->andWhere('t.contract = :contract')
                                ->setParameter('contract', $contract)
                                ->orderBy('t.month', 'ASC')
                            ;
                        },
                    ]);
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contract::class,
            'label_format' => 'contract.%name%',
            'translation_domain' => 'entities',
        ]);
    }
}
