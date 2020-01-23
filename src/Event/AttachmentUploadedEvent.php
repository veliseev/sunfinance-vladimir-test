<?php

namespace App\Event;


use App\Entity\Attachment;
use Symfony\Contracts\EventDispatcher\Event;

class AttachmentUploadedEvent extends Event
{
    public const NAME = 'attachment.uploaded';

    /**
     * @var Attachment
     */
    private $attachment;

    /**
     * @var string
     */
    private $uploadDir;

    public function __construct(Attachment $attachment, string $uploadDir)
    {
        $this->attachment = $attachment;
        $this->uploadDir  = $uploadDir;
    }

    /**
     * @return Attachment
     */
    public function getAttachment(): Attachment
    {
        return $this->attachment;
    }

    /**
     * @return string
     */
    public function getUploadDir(): string
    {
        return $this->uploadDir;
    }
}
