<?php

namespace Sllite\PanelBundle\Tests\Fixtures\Entity;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Sllite\PanelBundle\Entity\Site;

class LoadSiteData implements FixtureInterface
{
    static public $sites = [];

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $site = new Site();
        $site
            ->setName('Test site')
            ->setDomain('test-site.local');

        $manager->persist($site);
        $manager->flush();

        self::$sites[] = $site;
    }
}