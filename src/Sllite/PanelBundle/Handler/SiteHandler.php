<?php

namespace Sllite\PanelBundle\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;

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
     * @var ObjectRepository $repository
     */
    private $repository;

    /**
     * Конструктор.
     *
     * @param ObjectManager $om
     * @param string $entityClass
     */
    public function __construct(ObjectManager $om, $entityClass)
    {
        $this->om = $om;
        $this->repository = $this->om->getRepository($entityClass);
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        return $this->repository->find($id);
    }
}