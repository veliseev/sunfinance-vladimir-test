<?php

namespace App\EventListener;

use App\Entity\Attachment;
use App\Entity\Document;
use App\Entity\Thumbnail;
use App\Service\FileRemover;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

/**
 * Class AttachmentRemoveEventListener
 *
 * @package       App\EventListener
 */
class AttachmentRemoveEventListener
{
    /**
     * @var FileRemover
     */
    private $remover;

    /**
     * AttachmentRemoveEventListener constructor.
     *
     * @param FileRemover $remover
     */
    public function __construct(FileRemover $remover)
    {
        $this->remover = $remover;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Attachment || $entity instanceof Thumbnail) {
            $this->remover->remove($entity->getFilename());
        }
    }
}
