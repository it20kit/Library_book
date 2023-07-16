<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Component\HttpFoundation\Request;

class BookService
{

    public function __construct(
        private BookRepository $bookRepository,
        private RequestHandlerService $requestHandlerService
    )
    {
    }

    public function add_book_to_database(Book $book): void
    {
        $this->bookRepository->save($book);
    }

    public function createBookByDescription(Book $book, array $bookDescription): void
    {
        if (isset($bookDescription['title'])) {
            $book->setTitle($bookDescription['title']);
        }
        if (isset($bookDescription['description'])) {
            $book->setDescription($bookDescription['description']);
        }
        if (isset($bookDescription['cover'])) {
            $book->setCover($bookDescription['cover']);
        }
        if (isset($bookDescription['yearOfPublication'])) {
            $book->setYearOfPublication($bookDescription['yearOfPublication']);
        }
    }

    public function deleteBookByIdFromDatabase(int $bookId): void
    {
        $book = $this->getBookById($bookId);
        $this->bookRepository->remove($book);
    }

    public function isThereBookWithId(int $bookId): bool
    {
        return !$this->bookRepository->getBookById($bookId) == null;
    }

    public function getBookById(int $bookId): Book {
        $book = $this->bookRepository->getBookById($bookId);
        if ($book === null) {
            throw new \Exception('There is no book with this id in the database');
        }
        return $book;
    }

    public function getAuthorsDescriptionFromBookDescription(array $bookDescription): array
    {
        if (!isset($bookDescription['authors']))
        {
            throw new \Exception('author not found');
        }
        return $bookDescription['authors'];
    }

    public function getBookDescriptionFromRequest(Request $request): array
    {
        return $this->requestHandlerService->getDescriptionFromRequest($request);
    }

    public function thereIsBookWithThisTitleInDatabase(string $bookTitle): bool
    {
        return !$this->bookRepository->getBookByTitle($bookTitle) == null;
    }

    public function getBookTitleFromDescriptionBook(array $bookDescription): string
    {
        if (!isset($bookDescription['title'])) {
            throw new \Exception('book title not found');
        }
        return $bookDescription['title'];
    }

    public function createBookDescription(Book $book): array
    {
        $description = [];
        $description['id'] = $book->getId();
        $description['title'] = $book->getTitle();
        $description['description'] = $book->getDescription();
        $description['cover'] = $book->getCover();
        $description['yearOfPublication'] = $book->getYearOfPublication();
        return $description;
    }

    public function getBookByTitle(string $bookTitle): Book
    {
        $book = $this->bookRepository->getBookByTitle($bookTitle);
        if ($book === null) {
            throw new \Exception('book is not found');
        }
        return $book;
    }

    public function update(): void
    {
        $this->bookRepository->update();
    }
}