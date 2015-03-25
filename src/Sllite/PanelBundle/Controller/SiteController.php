<?php

namespace Sllite\PanelBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations;
use Sllite\PanelBundle\Model\SiteInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SiteController extends FOSRestController
{
    /**
     * Возвращает описание сайта.
     *
     * @Annotations\View(templateVar="site")
     *
     * @param int $id идентификатор сайта
     * @return SiteInterface
     */
    public function getSiteAction($id)
    {
        return $this->getIfExist($id);
    }

    /**
     * Возвращает сайт по ID, если он существует.
     *
     * @param int $id
     * @return SiteInterface
     *
     * @throw NotFoundHttpException в случае, если запрашиваемый ресурс не найден
     */
    protected function getIfExist($id)
    {
        $site = $this->container->get('sllite_panel.site.handler')->get($id);

        if (!$site instanceof SiteInterface) {
            throw new NotFoundHttpException(sprintf('Сайт с id %s не найден.', $id));
        }

        return $site;
    }
}
