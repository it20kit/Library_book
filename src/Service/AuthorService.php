<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Symfony\Component\HttpFoundation\Request;

class AuthorService
{
    public function __construct(
        private AuthorRepository $authorRepository,
        private RequestHandlerService $requestHandlerService,
    )
    {
    }

    public function createAuthorsByDescription(array $authorsDescription): array
    {
        $authors = [];
        foreach ($authorsDescription as $authorDescription) {
            $authors[] = $this->createAuthorByDescription($authorDescription);
        }
        return $authors;
    }

    public function isAuthorWithThatFullNameInDatabase(string $fullName): bool
    {
        return !$this->authorRepository->getAuthorByFullName($fullName) == null;
    }

    public function addAuthorInDataBase(Author $author): void
    {
        $this->authorRepository->save($author);
    }

    public function createAuthorByDescription($authorDescription): Author
    {
        $author = new Author();
        if (isset($authorDescription['fullName'])) {
            $author->setFullName($authorDescription['fullName']);
        }
        if (isset($authorDescription['biography'])) {
            $author->setBiography($authorDescription['biography']);
        }
        return $author;
    }

    public function update(): void
    {
        $this->authorRepository->update();
    }

    public function getAuthorByFullName(string $fullName): Author
    {
        $author = $this->authorRepository->getAuthorByFullName($fullName);
        if ($author == null) {
            throw new \Exception('author not found');
        }
        return $author;
    }

    public function createAuthorDescription(Author $author): array
    {
        $authorDescription = [];
        $authorDescription['id'] = $author->getId();
        $authorDescription['fullName'] = $author->getFullName();
        $authorDescription['biography'] = $author->getBiography();
        return  $authorDescription;
    }

    public function isAuthorWithThatIdInDatabase(int $authorId): bool
    {
        return !$this->authorRepository->getAuthorById($authorId) == null;
    }

    public function getAuthorById(int $authorId): Author
    {
        $author = $this->authorRepository->getAuthorById($authorId);
        if ($author == null) {
            throw new \Exception('author not found');
        }
        return $author;
    }

    public function deleteAuthorByFullName(string $fullName): void
    {
        $author = $this->getAuthorByFullName($fullName);
        $this->authorRepository->remove($author);
    }

    public function deleteAuthorById(int $authorId): void
    {
        $author = $this->getAuthorById($authorId);
        $this->authorRepository->remove($author);
    }

    public function createAuthorsDescriptionFromAuthors(array $authors): array
    {
        $authorDescription = [];
        foreach ($authors as $author) {
            $authorDescription[] = $this->createAuthorDescription($author);
        }

        return $authorDescription;
    }

    public function getListOfAuthorsByLimit(int $limit): array
    {
        return $this->authorRepository->getAuthorsByLimit($limit);
    }

    public function getDescriptionsOfAuthorsFromListAuthors(array $listOfAuthors): array
    {
        $authorsDescription = [];
        foreach ($listOfAuthors as $author) {
            $authorsDescription[] = $this->createAuthorDescription($author);
        }
        return $authorsDescription;
    }

    public function getAuthorDescriptionByRequest(Request $request): array
    {
        return $this->requestHandlerService->getDescriptionFromRequest($request);
    }

    public function updateAuthorByDescription(Author $author, array $description): void
    {
        if (isset($description['fullName'])) {
            $author->setFullName($description['fullName']);
        }
        if (isset($description['biography'])) {
            $author->setBiography($description['biography']);
        }
    }
}