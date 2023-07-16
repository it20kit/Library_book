<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function save(Book $book): void
    {
        $this->getEntityManager()->persist($book);
        $this->getEntityManager()->flush();
    }

    public function remove(Book $book): void
    {
        $this->getEntityManager()->remove($book);
        $this->getEntityManager()->flush();
    }

    public function getBookById(int $id): Book|null
    {
        return $this->find($id);
    }

    public function getBookByTitle(string $bookTitle): Book|null
    {
        return $this->findOneBy([
            'title' => $bookTitle
        ]);
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }
}