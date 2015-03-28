<?php

namespace Sllite\PanelBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sllite\PanelBundle\Exception\InvalidFormException;
use Sllite\PanelBundle\Model\SiteInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RestController extends FOSRestController
{
    /**
     * Возвращает описание сайта.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Возвращает информацию о сайте",
     *   statusCodes = {
     *     200 = "Возвращает в случае успеха",
     *     400 = "Возвращает в случае ошибки"
     *   }
     * )
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
     * @ApiDoc(
     *   resource = true,
     *   description = "Создаёт новый сайт",
     *   statusCodes = {
     *     201 = "Возвращает в случае успеха",
     *     400 = "Возвращает в случае ошибки"
     *   }
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
            $newSite = $this->container->get('sllite_panel.site.handler')->createNew(
                $request->request->all()
            );

            $this->container->get('sllite_panel.nginx.handler')->createHost($newSite);

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
     * @ApiDoc(
     *   resource = true,
     *   description = "Обновляет информацию о существующем или создаёт новый сайт",
     *   statusCodes = {
     *     201 = "Возвращает в случае успешного создания",
     *     204 = "Возвращает в случае успешного редактирования",
     *     400 = "Возвращает в случае ошибки"
     *   }
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
                $oldSite = clone $site;

                $statusCode = Codes::HTTP_NO_CONTENT;
                $newSite = $this->container->get('sllite_panel.site.handler')->editOrNew(
                    $site,
                    $request->request->all()
                );

                $this->container->get('sllite_panel.nginx.handler')->changeHost($oldSite, $newSite);
            } else {
                $statusCode = Codes::HTTP_CREATED;
                $site = $this->container->get('sllite_panel.site.handler')->createNew(
                    $request->request->all()
                );

                $this->container->get('sllite_panel.nginx.handler')->createHost($site);
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
     * Редактирует сайт.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Редактирует информацию о сайте",
     *   statusCodes = {
     *     204 = "Возвращает в случае успеха",
     *     400 = "Возвращает в случае ошибки"
     *   }
     * )
     *
     * @param Request $request
     * @param int $id
     * @return View|array|null
     *
     * @throws InvalidFormException в случае, если форма невалидная
     * @throws NotFoundHttpException в случае, если редактируемый сайт не существует
     */
    public function patchSiteAction(Request $request, $id)
    {
        try {
            $oldSite = clone $this->getIfExist($id);

            /** @var SiteInterface $newSite */
            $newSite = $this->container->get('sllite_panel.site.handler')->edit(
                $this->getIfExist($id),
                $request->request->all()
            );

            $this->container->get('sllite_panel.nginx.handler')->changeHost($oldSite, $newSite);

            return $this->routeRedirectView(
                'rest_get_site',
                [
                    'id' => $newSite->getId(),
                    '_format' => $request->get('_format')
                ],
                Codes::HTTP_NO_CONTENT
            );
        } catch (InvalidFormException $e) {
            return $e->getForm();
        }
    }

    /**
     * Возвращает список сайтов.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Возвращает список сайтов",
     *   statusCodes = {
     *     200 = "Возвращает в случае успеха",
     *     400 = "Возвращает в случае ошибки"
     *   }
     * )
     *
     * @Annotations\QueryParam(
     *  name="offset",
     *  requirements="\d+",
     *  nullable=true,
     *  description="Сдвиг с которого начинается выборка"
     * )
     *
     * @Annotations\QueryParam(
     *  name="limit",
     *  requirements="\d+",
     *  default=5,
     *  description="Количество выбираемых сайтов"
     * )
     *
     * @param ParamFetcherInterface $paramFetcher
     * @return array
     */
    public function getSitesAction(ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');

        return $this->container->get('sllite_panel.site.handler')->all($limit, $offset);
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
