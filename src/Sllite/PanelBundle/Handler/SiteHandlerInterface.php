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
    public function createNew(array $parameters);

    /**
     * Редактирует сайт или создаёт новый, если не существует.
     *
     * @param SiteInterface $site
     * @param array $parameters
     * @return SiteInterface
     */
    public function editOrNew(SiteInterface $site, array $parameters);

    /**
     * Редактирует сайт.
     *
     * @param SiteInterface $site
     * @param array $parameters
     * @return SiteInterface
     */
    public function edit(SiteInterface $site, array $parameters);

    /**
     * Возвращает список сайтов
     *
     * @param int $limit
     * @param int $offset
     * @param null $orderBy
     * @return array
     */
    public function all($limit = 5, $offset = 0, $orderBy = null);
}