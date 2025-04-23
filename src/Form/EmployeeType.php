<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Contract;
use App\Entity\Employee;
use App\Enum\EmployeeRole;
use App\Enum\EmployeeStatus;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSetDataEvent;
use Symfony\Component\Form\Event\SubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class EmployeeType extends AbstractType
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('birthDate', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('website')
            ->add('address', AddressType::class)
            ->add('isManager')
            ->add('roles', EnumType::class, [
                'class' => EmployeeRole::class,
                'expanded' => true,
                'label_attr' => [
                    'class' => 'checkbox-inline',
                ],
                'multiple' => true,
                'required' => false,
            ])
            ->add('status', EnumType::class, [
                'class' => EmployeeStatus::class,
                'label_attr' => [
                    'class' => 'radio-inline',
                ],
            ])
        ;

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (PreSetDataEvent $event): void {
                $form = $event->getForm();
                $employee = $event->getData();

                if ($employee->getId()) {
                    $form->add('currentContract', EntityType::class, [
                        'class' => Contract::class,
                        'choice_label' => function (Contract $contract) {
                            return $contract->getTitle()->getName()
                                .' ('
                                .$contract->getBeginDate()->format($this->translator->trans('date_format', [], 'messages'))
                                .' - '
                                .($contract->getEndDate()?->format($this->translator->trans('date_format', [], 'messages')) ?? $this->translator->trans('today', [], 'messages'))
                                .')'
                            ;
                        },
                        'query_builder' => function (EntityRepository $er) use ($employee): QueryBuilder {
                            return $er->createQueryBuilder('cc')
                                ->andWhere('cc.employee = :employee')
                                ->setParameter('employee', $employee)
                                ->orderBy('cc.beginDate', 'ASC')
                            ;
                        },
                    ]);
                }
            })
            ->addEventListener(FormEvents::SUBMIT, function (SubmitEvent $event): void {
                $employee = $event->getData();

                $roles = $employee->getRoles();
                sort($roles);
                $employee->setRoles($roles);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employee::class,
            'label_format' => 'employee.%name%',
            'translation_domain' => 'entities',
        ]);
    }
}
