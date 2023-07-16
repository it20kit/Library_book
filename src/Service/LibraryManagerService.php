<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;

class LibraryManagerService
{
    public function __construct(private BookService $bookService, private AuthorService $authorService)
    {
    }

    public function addBookByAuthorsToDatabase(Book $book, array $authors): void
    {
        /**
         * @var $author Author
         */
        foreach ($authors as $author) {
            $fullName = $author->getFullName();
            if (!$this->authorService->isAuthorWithThatFullNameInDatabase($fullName)) {
                $author->addBook($book);
                $this->authorService->addAuthorInDataBase($author);
            }
            $author =  $this->authorService->getAuthorByFullName($fullName);
            $author->addBook($book);
            $this->authorService->update();
        }
    }

    public function getBooksCollectionByAuthorFullName(string $authorFullName): Collection
    {
        $author = $this->authorService->getAuthorByFullName($authorFullName);
        return $author->getBook();
    }

    public function getBooksWithAuthorsFromBookCollection(Collection $bookCollection): array
    {
        $bookWithAuthor = [];
        $books = $bookCollection->toArray();
        /**
         * @var $book Book
         */
        foreach ($books as $book) {
            $bookDescription = $this->getBookDescriptionWithAuthorDescription($book);
            $bookWithAuthor[] = $bookDescription;
        }
        return $bookWithAuthor;
    }

    public function getBookDescriptionWithAuthorDescription(Book $book): array
    {
        $authors = $book->getAuthors()->toArray();
        $bookDescription = $this->bookService->createBookDescription($book);
        $authorsDescription = $this->authorService->getDescriptionsOfAuthorsFromListAuthors($authors);
        $bookDescription['authors'] = $authorsDescription;
        return $bookDescription;
    }
}