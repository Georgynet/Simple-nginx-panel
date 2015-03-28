<?php

namespace Sllite\PanelBundle\Handler;


use Sllite\PanelBundle\Model\SiteInterface;

/**
 * Интерфейс для работы с web-сервером.
 */
interface WebServerHandlerInterface
{
    /**
     * Создаёт новый хост.
     * @param SiteInterface $site сайт
     * @return mixed
     */
    public function createHost(SiteInterface $site);

    /**
     * Изменяет хост.
     * @param SiteInterface $oldSite старый сайт
     * @param SiteInterface $newSite новый сайт
     * @return mixed
     */
    public function changeHost(SiteInterface $oldSite, SiteInterface $newSite);

    /**
     * Возвращает путь до директории, в которой хранятся домены.
     * @return String
     */
    public function getSitesDirectory();
}