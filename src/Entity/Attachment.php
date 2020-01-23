<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation as JMS;
use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\OneToOne(targetEntity="Document", inversedBy="attachment")
     * @ORM\JoinColumn(name="document_id", referencedColumnName="id")
     * @var Document
     */
    private $document;

    /**
     * @ORM\OneToMany(targetEntity="Thumbnail", mappedBy="attachment", cascade={"all"})
     * @var PersistentCollection
     */
    private $thumbnails;

    /**
     * @Assert\File(
     *     mimeTypes = {"application/pdf", "application/x-pdf"},
     *     mimeTypesMessage = "Please upload a valid PDF"
     * )
     */
    private $file;

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
     *
     * @return Attachment
     */
    public function setDocument(Document $document): Attachment
    {
        $this->document = $document;

        return $this;
    }

    /**
     * @return PersistentCollection
     */
    public function getThumbnails(): ?PersistentCollection
    {
        return $this->thumbnails;
    }

    /**
     * @param PersistentCollection $thumbnails
     */
    public function setThumbnails(PersistentCollection $thumbnails): void
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
        $this->thumbnails[] = $thumbnail->setAttachment($this);

        return $this;
    }

    /**
     * @param Thumbnail $thumbnail
     */
    public function removeThumbnail(Thumbnail $thumbnail): void
    {
        $this->thumbnails->removeElement($thumbnail);
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setFile($file): void
    {
        $this->file = $file;
    }
}
