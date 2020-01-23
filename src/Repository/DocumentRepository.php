<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Document;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class DocumentRepository
 */
class DocumentRepository extends ServiceEntityRepository
{
    /**
     * DocumentRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    /**
     * @param Document $document
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Document $document): void
    {
        $this->_em->persist($document);
        $this->_em->flush();
    }

    /**
     * @param Document $document
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(Document $document): void
    {
        $this->_em->remove($document);
        $this->_em->flush();
    }
}
