<?php 
namespace App\Subscriber;

use App\Entity\Picture;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Vich\UploaderBundle\Storage\StorageInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Vich\UploaderBundle\Handler\DownloadHandler;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

use function PHPUnit\Framework\fileExists;

class ImageSubscriber implements EventSubscriberInterface
{
    private CacheManager $cacheManager;

    private UploaderHelper $helper;

    public function __construct(CacheManager $cacheManager, UploaderHelper $helper)
    {
        $this->cacheManager = $cacheManager;
        $this->helper = $helper;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::preRemove => 'preRemove'
        ];
    }
    public function preRemove(LifecycleEventArgs $lifecycleEventArgs)
    {
        if(!$lifecycleEventArgs->getObject() instanceof Picture)
        {
            return;
        }
        $picture = $lifecycleEventArgs->getObject();
        $imagePath = $this->helper->asset($picture, 'uploadedFile');
        $this->cacheManager->remove($imagePath, 'my_thumb');
        $this->cacheManager->remove($imagePath, 'my_first');
        $this->cacheManager->remove($imagePath, 'my_mini');
    }
}