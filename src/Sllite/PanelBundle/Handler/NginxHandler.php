<?php

namespace Sllite\PanelBundle\Handler;

use Sllite\PanelBundle\Model\SiteInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Handler для работы с web-сервером Nginx.
 */
class NginxHandler implements WebServerHandlerInterface
{
    /**
     * @var array $nginxSettings конфигурация для работы с Nginx
     */
    private $nginxSettings;
    /**
     * @var Filesystem $filesystem
     */
    private $filesystem;

    public function __construct(array $nginxSettings, Filesystem $filesystem)
    {
        $this->nginxSettings = $nginxSettings;
        $this->filesystem = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function createHost(SiteInterface $site)
    {
        $hostPath = $this->nginxSettings['sites_directory_root'] . $site->getDomain();
        $this->filesystem->mkdir($hostPath, 0755);
        $this->filesystem->touch($hostPath . '/index.html');
        file_put_contents($hostPath . '/index.html', $site->getDomain());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteHost(SiteInterface $site)
    {
        $hostDirRoot = Finder::create()->in(
            $this->nginxSettings['sites_directory_root'] . $site->getDomain()
        );
        $this->filesystem->remove($hostDirRoot);
    }

    /**
     * {@inheritdoc}
     */
    public function changeHost(SiteInterface $oldSite, SiteInterface $newSite)
    {
        if ($oldSite->getDomain() == $newSite->getDomain()) {
            return;
        }

        $this->filesystem->rename(
            $this->nginxSettings['sites_directory_root'] . $oldSite->getDomain(),
            $this->nginxSettings['sites_directory_root'] . $newSite->getDomain()
        );
    }
}