<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\SshKeyFormType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserCrudController extends AbstractCrudController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $encoder;

    /**
     * UserCrudController constructor.
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    /**
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('username'),
            TextField::new('plainPassword')->setFormType(PasswordType::class)->onlyOnForms(),
            EmailField::new('email'),
            AssociationField::new('groups')->hideOnIndex(),
            AssociationField::new('servers')->setFormTypeOptionIfNotSet('by_reference', false)->onlyOnForms(),
            CollectionField::new('servers')->onlyOnDetail(),
            CollectionField::new('sshKeys')
                ->setEntryType(SshKeyFormType::class)
                ->hideOnIndex()
                ->setRequired(true)
                ->setEntryIsComplex(true),
        ];
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param User $entityInstance
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $encodedPassword = $this->encoder->encodePassword($entityInstance, $entityInstance->getPlainPassword());
        $entityInstance->setPassword($encodedPassword);

        parent::persistEntity($entityManager, $entityInstance);
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param User $entityInstance
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance->getPlainPassword()) {
            $encodedPassword = $this->encoder->encodePassword($entityInstance, $entityInstance->getPlainPassword());
            $entityInstance->setPassword($encodedPassword);
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param $entityInstance
     */
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::deleteEntity($entityManager, $entityInstance);
    }

    /**
     * @param Actions $actions
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);

        $detailAction =  Action::new('detail')->linkToCrudAction('detail');

        $actions->add(Crud::PAGE_INDEX, $detailAction);

        return $actions;
    }
}
