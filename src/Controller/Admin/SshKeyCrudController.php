<?php

namespace App\Controller\Admin;

use App\Entity\SshKey;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class SshKeyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SshKey::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
