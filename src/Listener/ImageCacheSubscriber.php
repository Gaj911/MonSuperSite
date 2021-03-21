<?php
namespace App\Listener;

use App\Entity\Property;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;


// Permet grâce au Entity Listeners d'écouter une action, ici défacer l'img de liip du cache
class ImageCacheSubscriber implements EventSubscriber {

    /**
     * @var CacheManager
     */
    private $CacheManager;

     /**
     * @var UploaderHelper
     */
    private $UploaderHelper;

    public function __construct(CacheManager $CacheManager, UploaderHelper $UploaderHelper)
    {
        $this->CacheManager = $CacheManager;
        $this->UploaderHelper = $UploaderHelper;
    }

        public function getSubscribedEvents()
        {
            return [
                'preRemove',
                'preUpdate'
            ];
        }

        public function preRemove(LifecycleEventArgs $args) {

            $entity = $args->getEntity();
            if (!$entity instanceof Property) {
                return;
            }
            $this->CacheManager->remove($this->UploaderHelper->asset($entity, 'imageFile'));
        }

        public function preUpdate(PreUpdateEventArgs $args) {

                $entity = $args->getEntity();
                if (!$entity instanceof Property) {
                    return;
                }
                if ($entity->getImageFile() instanceof UploadedFile) {
                    $this->CacheManager->remove($this->UploaderHelper->asset($entity, 'imageFile'));
                }
        }

}

