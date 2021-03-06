<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation as JMS;
use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Thumbnail
 *
 * @ORM\Entity(repositoryClass="App\Repository\ThumbnailRepository")
 * @JMS\ExclusionPolicy("NONE")
 */
class Thumbnail
{

    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     * @SWG\Property(description="The unique identifier of the thumbnail.")
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
     * @JMS\Exclude()
     * @ORM\ManyToOne(targetEntity="Attachment", inversedBy="thumbnails")
     * @ORM\JoinColumn(name="attachment_id", referencedColumnName="id")
     * @var Attachment
     */
    private $attachment;

    /**
     * @Assert\Image()
     */
    private $file;

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
     * @return Attachment
     */
    public function getAttachment(): Attachment
    {
        return $this->attachment;
    }

    /**
     * @param Attachment $attachment
     *
     * @return Thumbnail
     */
    public function setAttachment(Attachment $attachment): Thumbnail
    {
        $this->attachment = $attachment;

        return $this;
    }
}
