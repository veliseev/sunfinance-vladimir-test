<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation as JMS;
use Swagger\Annotations as SWG;

/**
 * Class Attachment
 *
 * @ORM\Entity()
 * @JMS\ExclusionPolicy("NONE")
 */
class Attachment
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     * @SWG\Property(description="The unique identifier of the attachment.")
     *
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $filename;

    /**
     * @ORM\ManyToOne(targetEntity="Document", inversedBy="attachments")
     * @ORM\JoinColumn(name="document_id", referencedColumnName="id")
     * @var Document
     */
    private $document;

    /**
     * @ORM\OneToMany(targetEntity="Thumbnail", mappedBy="attachment")
     * @var ArrayCollection
     */
    private $thumbnails;

    /**
     * Attachment constructor.
     */
    public function __construct()
    {
        $this->thumbnails = new ArrayCollection();
    }

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
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     */
    public function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }

    /**
     * @return Document
     */
    public function getDocument(): Document
    {
        return $this->document;
    }

    /**
     * @param Document $document
     */
    public function setDocument(Document $document): void
    {
        $this->document = $document;
    }

    /**
     * @return ArrayCollection
     */
    public function getThumbnails(): ArrayCollection
    {
        return $this->thumbnails;
    }

    /**
     * @param ArrayCollection $thumbnails
     */
    public function setThumbnails(ArrayCollection $thumbnails): void
    {
        $this->thumbnails = $thumbnails;
    }

    /**
     * @param Thumbnail $thumbnail
     *
     * @return Attachment
     */
    public function addThumbnail(Thumbnail $thumbnail): Attachment
    {
        $thumbnail->setAttachment($this);
        $this->thumbnails[] = $thumbnail;

        return $this;
    }

    /**
     * @param Thumbnail $thumbnail
     */
    public function removeThumbnail(Thumbnail $thumbnail): void
    {
        $this->thumbnails->removeElement($thumbnail);
    }
}
