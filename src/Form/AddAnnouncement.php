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
            ->add('submit', SubmitType::class, ['label' => 'Submit Announcement'])
        ;
    

    }
}

// EOF

?>