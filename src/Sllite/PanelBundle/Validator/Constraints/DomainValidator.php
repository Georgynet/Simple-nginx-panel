<?php

namespace Sllite\PanelBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Валидатор доменного имени.
 */
class DomainValidator extends ConstraintValidator
{
    const PATTERN = '/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$/';

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

        if (preg_match(static::PATTERN, $value, $matches)) {

            if (0 !== mb_strpos($value, 'www.')) {
                return;
            }
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('%domain%', $value)
            ->addViolation();
    }
}