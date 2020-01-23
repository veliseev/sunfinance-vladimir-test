<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation as JMS;
use Swagger\Annotations as SWG;

/**
 * Class Document
 *
 * @ORM\Entity(repositoryClass="App\Repository\DocumentRepository")
 * @JMS\ExclusionPolicy("NONE")
 */
class Document
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     * @SWG\Property(description="The unique identifier of the document.")
     *
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $title;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return ArrayCollection
     */
    public function getAttachments(): ArrayCollection
    {
        return $this->attachments;
    }

    /**
     * @param ArrayCollection $attachments
     */
    public function setAttachments(ArrayCollection $attachments): void
    {
        $this->attachments = $attachments;
    }

    /**
     * @param Attachment $attachment
     *
     * @return Document
     */
    public function addAttachment(Attachment $attachment): Document
    {
        $attachment->setDocument($this);
        $this->attachments[] = $attachment;

        return $this;
    }

    /**
     * @param Attachment $attachment
     */
    public function removeAttachment(Attachment $attachment): void
    {
        $this->attachments->removeElement($attachment);
    }


    /**
     * @ORM\OneToMany(targetEntity="Attachment", mappedBy="document")
     * @var ArrayCollection
     */
    private $attachments;

    /**
     * Document constructor.
     */
    public function __construct()
    {
        $this->attachments = new ArrayCollection();
    }
}
