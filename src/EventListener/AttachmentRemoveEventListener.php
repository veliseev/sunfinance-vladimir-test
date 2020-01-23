<?php

namespace App\EventListener;

use App\Entity\Attachment;
use App\Entity\Document;
use App\Entity\Thumbnail;
use App\Service\FileRemover;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;


class AttachmentRemoveEventListener
{
    private $remover;

    public function __construct(FileRemover $remover)
    {
        $this->remover = $remover;
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Attachment || $entity instanceof Thumbnail) {
            $this->remover->remove($entity->getFilename());
        }
    }
}
