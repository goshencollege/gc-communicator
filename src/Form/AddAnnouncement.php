<?php


namespace App\Form;

use App\Entity\Category;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class AddAnnouncement extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subject', TextType::class)
            ->add('author', TextType::class)
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'query_builder' => function(EntityRepository $er)
                {
                    return $er->createQueryBuilder('a')
                        ->andWhere('a.active = :val')
                        ->setParameter('val', 1)
                        ->orderBy('a.name', 'ASC')
                    ;
                },
                'choice_label' => 'name',
                'placeholder' => 'Category',
            ])
            ->add('text', TextareaType::class)
            ->add('date', DateType::class, [
                'data' => new \DateTime,
            ])
            // ->add('freq', ChoiceType::class, [
            //       'label' => 'Recurrence',
            //       'choices' => [
            //         'None' => 'none',
            //         'Daily' => 'daily',
            //         'Weekly' => 'weekly',
            //         'Monthly' => 'monthly',
            //         'Yearly' => 'yearly',
            //       ],
            //       'data' => 'none',
            //       'expanded' => true,
            //     ])
            //     ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            //       $freq = $event->getData();
            //       $date_form = $event->getForm();
            
            //       if ($freq == 'daily')
            //       {
            //         $date_form->add('pattern', ChoiceType::class, [
            //             'label' => 'Daily',
            //             'choices' => [
            //               'Every' => 'every_x',
            //               'Every Weekday' => 'every_wkd',
            //             ],
            //             'data' => 'every',
            //             'expanded' => true,
            //           ])
            ->add('submit', SubmitType::class, ['label' => 'Submit Announcement'])
        ;
    

    }
}

// EOF

?>