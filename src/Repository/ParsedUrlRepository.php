<?php

namespace App\Repository;

use App\Entity\ParsedUrl;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ParsedUrl>
 *
 * @method ParsedUrl|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParsedUrl|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParsedUrl[]    findAll()
 * @method ParsedUrl[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParsedUrlRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ParsedUrl::class);
    }
}
