<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Book;
use App\Service\AuthorService;
use App\Service\BookService;
use App\Service\LibraryManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('library/')]
class LibraryController extends AbstractController
{
    public function __construct(
        private LibraryManagerService $libraryManagerService,
        private BookService           $bookService,
        private AuthorService         $authorService,
    )
    {
    }

    #[Route('book/add/')]
    public function addBookByAuthorToLibrary(Request $request): Response
    {
        $bookDescription = $this->bookService->getBookDescriptionFromRequest($request);
        $bookTitle = $this->bookService->getBookTitleFromDescriptionBook($bookDescription);
        if ($this->bookService->thereIsBookWithThisTitleInDatabase($bookTitle)) {
            return new JsonResponse([
                "status" => "The book $bookTitle is in the database"
            ]);
        }
        $authors = $this->authorService->createAuthorsByDescription(
            $this->bookService->getAuthorsDescriptionFromBookDescription($bookDescription)
        );
        $book = new Book();
        $this->bookService->createBookByDescription($book, $bookDescription);
        $this->bookService->add_book_to_database($book);
        $this->libraryManagerService->addBookByAuthorsToDatabase($book, $authors);
        return new JsonResponse([
            "status" => "The book $bookTitle added to the databases"
        ]);
    }

    #[Route('books/author/{authorFullName}')]
    public function getBooksByAuthorName(string $authorFullName): Response
    {
        if (!$this->authorService->isAuthorWithThatFullNameInDatabase($authorFullName)) {
            return new JsonResponse([
                'status' => "The author with that name is not in the library",
                'books' => null,
            ]);
        }
        $booksCollection = $this->libraryManagerService->getBooksCollectionByAuthorFullName($authorFullName);
        if ($booksCollection->count() === 0) {
            return new JsonResponse([
                'status' => "The author has no books",
                'books' => null,
            ]);
        }
        $books = $this->libraryManagerService->getBooksWithAuthorsFromBookCollection($booksCollection);
        return new JsonResponse([
            'status' => 'Books found',
            'books' => $books,
        ]);
    }

    #[Route('book/title/{bookTitle}')]
    public function getBookByTitle(string $bookTitle): Response
    {
        if ($this->bookService->thereIsBookWithThisTitleInDatabase($bookTitle)) {
            $book = $this->bookService->getBookByTitle($bookTitle);
            $bookDescription = $this->libraryManagerService->getBookDescriptionWithAuthorDescription($book);
            return new JsonResponse([
                'status' => "Book found",
                'book' => $bookDescription,
            ]);
        }
        return new JsonResponse([
            'status' => "Book $bookTitle not found",
            'book' => null,
        ]);
    }

    #[Route('book/id/{bookId}')]
    public function getBookById(int $bookId): Response
    {
        if ($this->bookService->isThereBookWithId($bookId)) {
            $book = $this->bookService->getBookById($bookId);
            $bookDescription = $this->libraryManagerService->getBookDescriptionWithAuthorDescription($book);
            return new JsonResponse([
                'status' => "Book found",
                'book' => $bookDescription
            ]);
        }
        return new JsonResponse([
            'status' => "Book not found",
            'book' => null,
        ]);
    }

    #[Route('book/update/id/{bookId}')]
    public function updateBookById(int $bookId, Request $request): Response
    {
        if (!$this->bookService->isThereBookWithId($bookId)) {
            return new JsonResponse([
                'status' => "Book $bookId not found",
                'book' => null,
            ]);
        }
        $book = $this->bookService->getBookById($bookId);
        $bookDescription = $this->bookService->getBookDescriptionFromRequest($request);
        $authors = $this->authorService->createAuthorsByDescription(
            $this->bookService->getAuthorsDescriptionFromBookDescription($bookDescription)
        );
        $this->bookService->createBookByDescription($book, $bookDescription);
        $this->bookService->update();
        $this->libraryManagerService->addBookByAuthorsToDatabase($book, $authors);
        return new JsonResponse([
            'status' => "Book $bookId updated",
            'book' => $this->bookService->createBookDescription($book),
        ]);
    }
}