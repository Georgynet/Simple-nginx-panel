<?php

namespace Sllite\PanelBundle\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Sllite\PanelBundle\Exception\InvalidFormException;
use Sllite\PanelBundle\Form\SiteType;
use Sllite\PanelBundle\Model\SiteInterface;
use Symfony\Component\Form\FormFactory;

/**
 * Handler для работы с сайтами.
 */
class SiteHandler implements SiteHandlerInterface
{
    /**
     * @var ObjectManager $om
     */
    private $om;
    /**
     * @var string $entityClass класс описывающий сайт
     */
    private $entityClass;
    /**
     * @var ObjectRepository $repository
     */
    private $repository;
    /**
     * @var FormFactory $formFactory
     */
    private $formFactory;

    /**
     * Конструктор.
     *
     * @param ObjectManager $om
     * @param string $entityClass
     * @param FormFactory $formFactory
     */
    public function __construct(ObjectManager $om, $entityClass, FormFactory $formFactory)
    {
        $this->om = $om;
        $this->entityClass = $entityClass;
        $this->repository = $this->om->getRepository($this->entityClass);
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        return $this->repository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function post(array $parameters)
    {
        return $this->processForm(
            $this->createSite(),
            $parameters,
            'POST'
        );
    }

    /**
     * Обработка формы.
     *
     * @param SiteInterface $site
     * @param array $parameters
     * @param string $method
     *
     * @return SiteInterface
     *
     * @throws InvalidFormException в случае, если отправленна не валидная форма
     */
    private function processForm(SiteInterface $site, array $parameters, $method = 'PUT')
    {
        $form = $this->formFactory->create(new SiteType(), $site, ['method' => $method]);

        $form->submit($parameters, 'PATCH' != $method);

        if (!$form->isValid()) {
            throw new InvalidFormException('Отправленны невалидные данные', $form);
        }

        $site = $form->getData();
        $this->om->persist($site);
        $this->om->flush();

        return $site;
    }

    /**
     * Возвращает объект для нового сайта.
     * @return SiteInterface
     */
    private function createSite()
    {
        return new $this->entityClass;
    }
}