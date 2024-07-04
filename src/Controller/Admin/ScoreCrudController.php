<?php

namespace App\Controller\Admin;

use App\Entity\Score;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ScoreCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Score::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('score'),
            TextEditorField::new('description'),
        ];
    }
    
}
