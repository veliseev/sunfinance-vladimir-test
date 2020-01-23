<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation as JMS;
use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank()
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $title;

    /**
     * @ORM\OneToOne(targetEntity="Attachment", mappedBy="document", cascade={"all"})
     * @var Attachment
     */
    private $attachment;

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
     * @return string|null
     */
    public function getTitle(): ?string
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
     * @return Attachment|null
     */
    public function getAttachment(): ?Attachment
    {
        return $this->attachment;
    }

    /**
     * @param Attachment $attachment
     */
    public function setAttachment(Attachment $attachment): void
    {
        $this->attachment = $attachment->setDocument($this);
    }
}
