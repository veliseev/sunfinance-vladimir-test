<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Document;
use App\Entity\Thumbnail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class ThumbnailRepository
 */
class ThumbnailRepository extends ServiceEntityRepository
{
    /**
     * DocumentRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Thumbnail::class);
    }

    /**
     * @param Thumbnail $thumbnail
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Thumbnail $thumbnail): void
    {
        $this->_em->persist($thumbnail);
        $this->_em->flush();
    }

    public function findThumbnailById(Document $document, $thumbnailId)
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.attachment', 'ta')
            ->where('ta.document = :document')
            ->andWhere('t = :thumb')
            ->setParameter('document', $document)
            ->setParameter('thumb', $thumbnailId)
            ->getQuery()
            ->getSingleResult(AbstractQuery::HYDRATE_OBJECT);
    }

    /**
     * @param Thumbnail $thumbnail
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(Thumbnail $thumbnail): void
    {
        $this->_em->remove($thumbnail);
        $this->_em->flush();
    }
}
