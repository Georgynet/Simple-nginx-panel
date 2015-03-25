<?php

namespace Sllite\PanelBundle\Handler;

use Sllite\PanelBundle\Model\SiteInterface;

/**
 * Интерфейс SiteHandler.
 */
interface SiteHandlerInterface
{
    /**
     * Возвращает сайт по ID.
     *
     * @param int $id
     * @return SiteInterface
     */
    public function get($id);

    /**
     * Создаёт новый сайт.
     *
     * @param array $parameters
     * @return SiteInterface
     */
    public function post(array $parameters);
}