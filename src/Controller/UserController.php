<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Services\UserGrudService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\User\UserInvalidException;

class UserController extends AbstractController
{
    #[Route('/users', name: 'users_get', methods: 'GET')]
    public function index(Request $request, UserGrudService $userGrudService): JsonResponse
    {
        $users = $userGrudService->getList($request->query->all());

        return $this->json($users);
    }

    #[Route('/users/{id}', name: 'users_show', methods: 'GET')]
    public function show(User $user): JsonResponse
    {
        return $this->json($user);
    }

    #[Route('/users', name: 'user_create', methods: 'POST')]
    public function create(Request $request, UserGrudService $userGrudService): JsonResponse
    {
        try {
            $userGrudService->create($request->request->get('user'));

            return $this->json(['status' => 'success']);
        } catch (UserInvalidException $exception) {
            return $this->json([
                'status' => 'error',
                'error' => $exception->getMessage()
            ]);
        }
    }

    #[Route('/users/{id}/edit', name: 'user_edit', methods: 'PUT')]
    public function edit(User $user, Request $request, UserGrudService $userGrudService): JsonResponse
    {
        try {
            $userGrudService->update($user, $request->request->get('user'));

            return $this->json(['status' => 'success']);
        } catch (UserInvalidException $exception) {
            return $this->json([
                'status' => 'error',
                'error' => $exception->getMessage()
            ]);
        }
    }
}
