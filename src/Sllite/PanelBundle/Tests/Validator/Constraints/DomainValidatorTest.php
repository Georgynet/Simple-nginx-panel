<?php
/**
 * Created by PhpStorm.
 * User: georgy
 * Date: 29.03.15
 * Time: 16:42
 */

namespace Sllite\PanelBundle\Tests\Validator\Constraints;

use Sllite\PanelBundle\Validator\Constraints\Domain;
use Sllite\PanelBundle\Validator\Constraints\DomainValidator;
use Symfony\Component\Validator\Tests\Constraints\AbstractConstraintValidatorTest;
use Symfony\Component\Validator\Validation;

class DomainValidatorTest extends AbstractConstraintValidatorTest
{

    protected function getApiVersion()
    {
        return Validation::API_VERSION_2_5;
    }

    protected function createValidator()
    {
        return new DomainValidator();
    }

    public function testNullIsValid()
    {
        $this->validator->validate(null, new Domain());

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getValidDomains
     */
    public function testValidDomains($domain)
    {
        $this->validator->validate($domain, new Domain());

        $this->assertNoViolation();
    }

    public function getValidDomains()
    {
        return [
            ['domain.ru'],
            ['qwe.www.domain.ru'],
            ['sub-domain.domain.com'],
            ['dom-ain.com']
        ];
    }

    /**
     * @dataProvider getInvalidDomains
     */
    public function testInvalidDomains($domain)
    {
        $constraint = new Domain([
            'message' => 'myMessage',
        ]);

        $this->validator->validate($domain, $constraint);

        $this->buildViolation('myMessage')
            ->setParameter('%domain%', $domain)
            ->assertRaised();
    }

    public function getInvalidDomains()
    {
        return [
            ['local'],
            ['www.local'],
            ['www.host.local'],
        ];
    }
}