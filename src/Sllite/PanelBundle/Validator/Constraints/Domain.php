<?php

namespace Sllite\PanelBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Constraint доменного имени.
 */
class Domain extends Constraint
{
    public $message = 'Домен "%domain%" задан в неправильной форме. Формат (без www): sub-domain.domain.com';
}