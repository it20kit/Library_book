<?php

namespace App\Controller;

use App\Service\AuthorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/author/')]
class AuthorController extends AbstractController
{

    public function __construct(private AuthorService $authorService)
    {
    }

    #[Route('add/')]
    public function addAuthor(Request $request): Response
    {
        $authorDescription = $this->authorService->getAuthorDescriptionByRequest($request);
        $author = $this->authorService->createAuthorByDescription($authorDescription);
        $this->authorService->addAuthorInDataBase($author);
        $fullNameAuthor = $author->getFullName();
        return new JsonResponse([
            'status' => "Author $fullNameAuthor added",
            'author' => $this->authorService->createAuthorDescription($author),
        ]);
    }

    #[Route('name/{fullName}')]
    public function getAuthorByName(string $fullName): Response
    {
        if (!$this->authorService->isAuthorWithThatFullNameInDatabase($fullName)) {
            return new JsonResponse([
                'author' => "Author by name is not found"
            ]);
        }
        $author = $this->authorService->getAuthorByFullName($fullName);
        $authorDescription = $this->authorService->createAuthorDescription($author);
        return new JsonResponse([
            'author' => $authorDescription
        ]);
    }

    #[Route('id/{authorId}')]
    public function getAuthorById(int $authorId): Response
    {
        if (!$this->authorService->isAuthorWithThatIdInDatabase($authorId)) {
            return new JsonResponse([
                'author' => "Author by id is not found"
            ]);
        }
        $author = $this->authorService->getAuthorById($authorId);
        $authorDescription = $this->authorService->createAuthorDescription($author);
        return new JsonResponse([
            'author' => $authorDescription,
        ]);
    }

    #[Route('delete/name/{fullName}')]
    public function deleteAuthorByFullName(string $fullName): Response
    {
        if ($this->authorService->isAuthorWithThatFullNameInDatabase($fullName)) {
            $this->authorService->deleteAuthorByFullName($fullName);
            return new JsonResponse([
                'status' => "Author $fullName is deleted database"
            ]);
        }
        return new JsonResponse([
            'status' => "Author is not found"
        ]);
    }

    #[Route('delete/id/{authorId}')]
    public function deleteAuthorById(int $authorId): Response
    {
        if ($this->authorService->isAuthorWithThatIdInDatabase($authorId)) {
            $this->authorService->deleteAuthorById($authorId);
            return new JsonResponse([
                'status' => "Author $authorId is deleted database"
            ]);
        }
        return new JsonResponse([
            'status' => "Author $authorId is not found",
        ]);
    }

    #[Route('limit/{limit}')]
    public function getListOfAuthorsByLimit(int $limit): Response
    {
        $listOfAuthors = $this->authorService->getListOfAuthorsByLimit($limit);
        return new JsonResponse([
            'authors' => $this->authorService->getDescriptionsOfAuthorsFromListAuthors($listOfAuthors),
        ]);
    }

    #[Route('update/id/{authorId}')]
    public function updateAuthorById(int $authorId, Request $request): Response
    {
        if (!$this->authorService->isAuthorWithThatIdInDatabase($authorId)) {
            return new JsonResponse([
                'status' => "Author $authorId not found",
                'author' => null,
            ]);
        }
        $authorDescription = $this->authorService->getAuthorDescriptionByRequest($request);
        $author = $this->authorService->getAuthorById($authorId);
        $this->authorService->updateAuthorByDescription($author, $authorDescription);
        $this->authorService->update();
        return new JsonResponse([
            'status' => "author $authorId updated",
            'author' => $this->authorService->createAuthorDescription($author),
        ]);
    }
}