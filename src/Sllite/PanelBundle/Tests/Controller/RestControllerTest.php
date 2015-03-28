<?php

namespace Sllite\PanelBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Sllite\PanelBundle\Handler\NginxHandler;
use Sllite\PanelBundle\Model\SiteInterface;
use Sllite\PanelBundle\Tests\Fixtures\Entity\LoadSiteData;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class RestControllerTest extends WebTestCase
{
    /**
     * @var Client
     */
    private $client;
    /**
     * @var Filesystem
     */
    private $fs;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->fs = new Filesystem();
    }

    public function testGetSiteSuccessful()
    {
        $fixtures = ['Sllite\PanelBundle\Tests\Fixtures\Entity\LoadSiteData'];
        $this->loadFixtures($fixtures);
        $sites = LoadSiteData::$sites;

        /** @var SiteInterface $site */
        $site = array_pop($sites);

        $this->client->request(
            'GET',
            $this->getUrl('rest_get_site', ['id' => $site->getId(), '_format' => 'json']),
            ['ACCEPT' => 'application/json']
        );

        $decoded = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );

        $this->assertArrayHasKey('id', $decoded);
    }

    public function testGetSiteNotFound()
    {
        $this->client->request(
            'GET',
            $this->getUrl('rest_get_site', ['id' => '0', '_format' => 'json']),
            ['ACCEPT' => 'application/json']
        );

        $this->assertTrue($this->client->getResponse()->isNotFound());
    }

    public function testCreateSiteSuccessful()
    {
        $this->client->request(
            'POST',
            $this->getUrl('rest_post_site', ['_format' => 'json']),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"name" : "New site", "domain" : "new-site.local"}'
        );

        $this->assertEquals(Codes::HTTP_CREATED, $this->client->getResponse()->getStatusCode());

        $domainsRoot = $this->getContainer()->get('sllite_panel.nginx.handler')->getSitesDirectory();
        $this->assertTrue($this->fs->exists($domainsRoot . '/new-site.local'));
    }

    public function testCreateSiteFail()
    {
        $this->client->request(
            'POST',
            $this->getUrl('rest_post_site', ['_format' => 'json']),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"name" : "New site", "domains" : "test.local"}'
        );

        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());

        $domainsRoot = $this->getContainer()->get('sllite_panel.nginx.handler')->getSitesDirectory();
        $this->assertFalse($this->fs->exists($domainsRoot . '/test.local'));
    }

    public function testPutSiteModify()
    {
        $fixtures = ['Sllite\PanelBundle\Tests\Fixtures\Entity\LoadSiteData'];
        $this->loadFixtures($fixtures);
        $sites = LoadSiteData::$sites;

        /** @var SiteInterface $site */
        $site = array_pop($sites);

        $this->client->request(
            'GET',
            $this->getUrl('rest_get_site', ['id' => $site->getId(), '_format' => 'json']),
            ['ACCEPT' => 'application/json']
        );

        $this->assertEquals(
            Codes::HTTP_OK,
            $this->client->getResponse()->getStatusCode(),
            $this->client->getResponse()->getContent()
        );

        $this->client->request(
            'PUT',
            $this->getUrl('rest_put_site', ['id' => $site->getId(), '_format' => 'json']),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"name" : "name-after-put", "domain" : "domain-after-put.local"}'
        );

        $this->assertEquals(
            Codes::HTTP_NO_CONTENT,
            $this->client->getResponse()->getStatusCode(),
            $this->client->getResponse()->getContent()
        );

        $domainsRoot = $this->getContainer()->get('sllite_panel.nginx.handler')->getSitesDirectory();
        $this->assertTrue($this->fs->exists($domainsRoot . '/domain-after-put.local'));

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Location',
                'http://localhost' . $this->getUrl('rest_get_site', ['id' => $site->getId(), '_format' => 'json'])
            ),
            $this->client->getResponse()->headers
        );
    }

    public function testPutSiteCreate()
    {
        $id = 0;
        $this->client->request(
            'GET',
            $this->getUrl('rest_get_site', ['id' => $id, '_format' => 'json']),
            ['ACCEPT' => 'application/json']
        );

        $this->assertEquals(
            Codes::HTTP_NOT_FOUND,
            $this->client->getResponse()->getStatusCode(),
            $this->client->getResponse()->getContent()
        );

        $domainsRoot = $this->getContainer()->get('sllite_panel.nginx.handler')->getSitesDirectory();
        if ($this->fs->exists($domainsRoot . '/domain-after-put.local')) {
            $this->fs->remove($domainsRoot . '/domain-after-put.local');
        }
        if (!$this->fs->exists($domainsRoot . '/test-site.local')) {
            $this->fs->mkdir($domainsRoot . '/test-site.local');
        }

        $this->client->request(
            'PUT',
            $this->getUrl('rest_put_site', ['id' => $id, '_format' => 'json']),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"name" : "name-after-create", "domain" : "domain-after-create.local"}'
        );

        $this->assertTrue($this->fs->exists($domainsRoot . '/domain-after-create.local'));

        $this->assertEquals(
            Codes::HTTP_CREATED,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testPatchSite()
    {
        $fixtures = ['Sllite\PanelBundle\Tests\Fixtures\Entity\LoadSiteData'];
        $this->loadFixtures($fixtures);
        $sites = LoadSiteData::$sites;

        /** @var SiteInterface $site */
        $site = array_pop($sites);

        $this->client->request(
            'GET',
            $this->getUrl('rest_get_site', ['id' => $site->getId(), '_format' => 'json']),
            ['ACCEPT' => 'application/json']
        );

        $this->assertEquals(
            Codes::HTTP_OK,
            $this->client->getResponse()->getStatusCode(),
            $this->client->getResponse()->getContent()
        );

        $this->client->request(
            'PATCH',
            $this->getUrl('rest_patch_site', ['id' => $site->getId(), '_format' => 'json']),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"name" : "name-after-patch"}'
        );

        $this->assertEquals(
            Codes::HTTP_NO_CONTENT,
            $this->client->getResponse()->getStatusCode()
        );

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Location',
                'http://localhost' . $this->getUrl('rest_get_site', ['id' => $site->getId(), '_format' => 'json'])
            ),
            $this->client->getResponse()->headers
        );
    }
}
