<?php

namespace Sllite\PanelBundle\Model;

/**
 * Интерфейс описывающий сайт.
 */
interface SiteInterface
{
    /**
     * Возвращает идентификатор сайта.
     * @return int
     */
    public function getId();

    /**
     * Устанавливает имя сайта.
     * @param $name
     * @return SiteInterface
     */
    public function setName($name);

    /**
     * Возвращает имя сайта.
     * @return string
     */
    public function getName();

    /**
     * Устанавливает домен сайта.
     * @param $domain
     * @return SiteInterface
     */
    public function setDomain($domain);

    /**
     * Возвращает домен сайта.
     * @return string
     */
    public function getDomain();
}