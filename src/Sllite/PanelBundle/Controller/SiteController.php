<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 27.03.2015
 * Time: 14:03
 */

namespace Sllite\PanelBundle\Controller;

use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sllite\PanelBundle\Exception\InvalidFormException;
use Sllite\PanelBundle\Form\SiteType;
use Sllite\PanelBundle\Model\SiteInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SiteController extends FOSRestController
{
    /**
     * Возвращает список сайтов.
     *
     * @Route("/", name="main")
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
     * @Annotations\View(
     *  templateVar = "sites"
     * )
     *
     * @param ParamFetcherInterface $paramFetcher
     * @return mixed
     */
    public function indexAction(ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');

        $sites = $this->container->get('sllite_panel.site.handler')->all($limit + 1, $offset);

        return [
            'hasNext' => (count($sites) > 5) ? (bool) array_pop($sites) : false,
            'sites' => $sites,
            'limit' => $limit,
            'offset' => $offset
        ];
    }

    /**
     * Возвращает форму для создания нового сайта.
     *
     * @Route("/new", name="new_site_form", methods="get")
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
     * Создаёт новый сайт.
     *
     * @Route("/new", name="new_site", methods="post")
     *
     * @param Request $request
     *
     * @return View|array|null
     *
     * @throws InvalidFormException в случае, если форма невалидная
     */
    public function saveNewSite(Request $request)
    {
        try {
            /** @var SiteInterface $newSite */
            $this->container->get('sllite_panel.site.handler')->createNew(
                $request->request->all()
            );

            return $this->routeRedirectView(
                'main',
                [],
                Codes::HTTP_CREATED
            );
        } catch (InvalidFormException $e) {
            return $e->getForm();
        }
    }

    /**
     * Возвращает форму для редактирования сайта.
     *
     * @Route("/edit/{id}", name="edit_site", methods="get")
     *
     * @Annotations\View()
     *
     * @param int $id
     * @return \Symfony\Component\Form\Form
     */
    public function getEditSiteAction($id)
    {
        return [
            'form' => $this->createForm(
                new SiteType(),
                $this->getIfExist($id)
            )
        ];
    }

    /**
     * Сохраняет отредактированный сайта.
     *
     * @Route("/edit/{id}", name="save_site", methods="post")
     *
     * @Annotations\View(
     *  template="SllitePanelBundle:Site:getEditSite.html.twig",
     *  templateVar="form"
     * )
     *
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\Form\Form
     */
    public function saveEditSiteAction(Request $request, $id)
    {
        try {
            /** @var SiteInterface $site */
            $site = $this->container->get('sllite_panel.site.handler')->edit(
                $this->getIfExist($id),
                $request->request->all()
            );

            return $this->routeRedirectView(
                'edit_site',
                [
                    'id' => $site->getId()
                ],
                Codes::HTTP_NO_CONTENT
            );
        } catch (InvalidFormException $e) {
            return $e->getForm();
        }
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
