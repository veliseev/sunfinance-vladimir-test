<?php

namespace App\EventListener;

use App\Entity\Thumbnail;
use App\Event\AttachmentUploadedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Spatie\PdfToImage\Exceptions\PdfDoesNotExist;
use Spatie\PdfToImage\Pdf;

/**
 * Class AttachmentEventListener
 *
 * @package       App\EventListener
 */
class AttachmentEventListener
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * AttachmentEventListener constructor.
     *
     * @param LoggerInterface        $logger
     * @param EntityManagerInterface $manager
     */
    public function __construct(LoggerInterface $logger, EntityManagerInterface $manager)
    {
        $this->logger  = $logger;
        $this->manager = $manager;
    }

    /**
     * @param AttachmentUploadedEvent $event
     *
     * @throws \Spatie\PdfToImage\Exceptions\InvalidFormat
     */
    public function onAttachmentUploaded(AttachmentUploadedEvent $event)
    {
        $attachment = $event->getAttachment();
        try {
            $pdf = new Pdf(sprintf('%s/%s', $event->getUploadDir(), $attachment->getFilename()));
            foreach ($attachment->getThumbnails() as $thumbnail) {
                $this->manager->remove($thumbnail);
            }
            $thumbnails = $pdf->setOutputFormat('png')->saveAllPagesAsImages(
                $event->getUploadDir(),
                uniqid($attachment->getDocument()->getTitle())
            );
            foreach ($thumbnails as $path) {
                $thumbnail = new Thumbnail();
                $thumbnail->setFilename(pathinfo($path, PATHINFO_BASENAME));
                $attachment->addThumbnail($thumbnail);
            }

            $this->manager->persist($attachment);
            $this->manager->flush();

        } catch (PdfDoesNotExist $ex) {
            $this->logger->error('File not found', $ex->getMessage());
        }
    }
}
