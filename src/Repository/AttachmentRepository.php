<?php

namespace App\Repository;

use App\Entity\Attachment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method Attachment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Attachment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Attachment[]    findAll()
 * @method Attachment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttachmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attachment::class);
    }

    public function findAttachmentsToRemove(array $filesnames, ?int $post_id)
    {
        $qd = $this->createQueryBuilder('a');

        $qd->select()
           ->where(
                $qd->expr()->andX(
                    $qd->expr()->eq('a.post', $post_id),
                    $qd->expr()->notIn('a.filename', $filesnames)
                )
           );

        return $qd->getQuery()->getResult();
    }


}
