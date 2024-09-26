<?php

namespace App\Form\Type;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastname', TextType::class, [
                'label' => 'lastname',
                'required' => true
            ])
            ->add('firstname', TextType::class, [
                'label' => 'firstname',
                'required' => true
            ])
            ->add('email', EmailType::class, [
                'label' => 'email',
                'required' => true
            ])
            ->add('company', TextType::class, [
                'label' => 'company',
                'required' => true
            ])
            ->add('street', TextType::class, [
                'label' => 'street',
                'required' => true
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'postal_code',
                'required' => true
            ])
            ->add('city', TextType::class, [
                'label' => 'city',
                'required' => true
            ])
            ->add('comment', TextType::class, [
                'label' => 'comment',
                'required' => true
            ])
            ->add('attachedFile', FileType::class, [
                'label' => 'attached_file',
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                            'image/jpeg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'please_upload_pdf_jpg_or_png'
                    ])
                ],
                'mapped' => false,
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}