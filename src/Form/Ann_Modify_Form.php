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
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class AnnModifyForm extends AbstractType
{

    public function __construct()
    {
      $this->date = new \DateTime('tomorrow', new \DateTimeZone('GMT'));
  
    }

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
          'placeholder' => 'Select a Category',
        ])
        ->add('text', TextareaType::class)
        ->add('start_date', DateType::class, [
            'widget' => 'single_text',
            'format' => 'MM/dd/yyyy',
            'html5' => false,
          ])
        ->add('continue_date', ChoiceType::class, [
            'choices' => range(1, 5),
            'choice_label' => function($choice, $key, $value) {
              return (string)$choice.' Day(s)';
            },
        ])
        ->add('announcementFile', VichFileType::class, [
          'mapped' => true,
          'required' => false,
        ])
        ->add('submit', SubmitType::class, [
          'label' => 'Submit Announcement'
        ])
      ;

    }
}

// EOF

?>
