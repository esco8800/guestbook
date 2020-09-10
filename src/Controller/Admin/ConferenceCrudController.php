<?php

namespace App\Controller\Admin;

use App\Entity\Conference;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ConferenceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Conference::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('city'),
            TextField::new('year'),
            TextField::new('slug'),
            BooleanField::new('isInternational'),
        ];
    }
}
