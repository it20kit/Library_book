<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    public function save(Author $author): void
    {
        $this->getEntityManager()->persist($author);
        $this->getEntityManager()->flush();
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function getAuthorByFullName(string $fullName): Author|null
    {
        return $this->findOneBy([
            'fullName' => $fullName
        ]);
    }

    public function getAuthorById(int $authorId): Author|null
    {
        return $this->find($authorId);
    }

    public function remove(Author $author): void
    {
        $this->getEntityManager()->remove($author);
        $this->getEntityManager()->flush();
    }

    public function getAuthorsByLimit(int $limit): array
    {
        return $this->createQueryBuilder('a')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}