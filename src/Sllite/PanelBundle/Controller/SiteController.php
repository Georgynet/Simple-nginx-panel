<?php

namespace Sllite\PanelBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Sllite\PanelBundle\Exception\InvalidFormException;
use Sllite\PanelBundle\Form\SiteType;
use Sllite\PanelBundle\Model\SiteInterface;
use Symfony\Component\HttpFoundation\Request;
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
     * Создаёт сайт.
     *
     * @Annotations\View(
     *  template = "SllitePanelBundle:Site:newSite.html.twig",
     *  statusCode = Codes::HTTP_BAD_REQUEST,
     *  templateVar = "form"
     * )
     *
     * @param Request $request
     * @return View|array|null
     *
     * @throws InvalidFormException в случае, если форма невалидная
     */
    public function postSiteAction(Request $request)
    {
        try {
            /** @var SiteInterface $newSite */
            $newSite = $this->container->get('sllite_panel.site.handler')->post(
                $request->request->all()
            );

            return $this->routeRedirectView(
                'rest_get_site',
                [
                    'id' => $newSite->getId(),
                    '_format' => $request->get('_format')
                ],
                Codes::HTTP_CREATED
            );
        } catch (InvalidFormException $e) {
            return $e->getForm();
        }
    }

    /**
     * Обновляет сущесвующий сайт или создаёт новый.
     *
     * @Annotations\View(
     *  template = "SllitePanelBundle:Site:editSite.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request
     * @param int $id
     * @return View|array|null
     *
     * @throws InvalidFormException в случае, если форма невалидная
     */
    public function putSiteAction(Request $request, $id)
    {
        try {
            $site = $this->container->get('sllite_panel.site.handler')->get($id);

            if ($site instanceof SiteInterface) {
                $statusCode = Codes::HTTP_NO_CONTENT;
                $this->container->get('sllite_panel.site.handler')->put(
                    $site,
                    $request->request->all()
                );

            } else {
                $statusCode = Codes::HTTP_CREATED;
                $site = $this->container->get('sllite_panel.site.handler')->post(
                    $request->request->all()
                );
            }

            return $this->routeRedirectView(
                'rest_get_site',
                [
                    'id' => $site->getId(),
                    '_format' => $request->get('_format')
                ],
                $statusCode
            );
        } catch (InvalidFormException $e) {
            return $e->getForm();
        }
    }

    /**
     * Возвращает форму для создания нового сайта.
     *
     * @Annotations\View()
     *
     * @return \Symfony\Component\Form\Form
     */
    public function newSiteAction()
    {
        return $this->createForm(new SiteType());
    }

    /**
     * Возвращает сайт по ID, если он существует.
     *
     * @param int $id
     * @return SiteInterface
     *
     * @throws NotFoundHttpException в случае, если запрашиваемый ресурс не найден
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
