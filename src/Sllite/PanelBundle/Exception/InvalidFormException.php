<?php
/**
 * Created by PhpStorm.
 * User: georgy
 * Date: 25.03.15
 * Time: 12:55
 */

namespace Sllite\PanelBundle\Exception;

/**
 * Исключение для невалидной формы.
 */
class InvalidFormException extends \RuntimeException
{
    private $form;

    public function __construct($message, $form = null)
    {
        parent::__construct($message);

        $this->form = $form;
    }

    /**
     * @return array|null
     */
    public function getForm()
    {
        return $this->form;
    }
}