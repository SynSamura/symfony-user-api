<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/user')]
class UserController extends AbstractController
{
    #[Route(
        path: '/',
        name: 'user-list',
        methods: ['GET']
    )]
    public function list(UserRepository $userRepository): Response
    {
        $userList = $userRepository->findAll();

        return $this->json($userList);
    }

    #[Route(
        path: '/{id}',
        name: 'user-show',
        methods: ['GET']
    )]
    public function show(int $id, UserRepository $userRepository): Response
    {

        $user = $userRepository->find($id);

        return $this->json($user);
    }


    #[Route(
        path: '/',
        name: 'user-create',
        methods: ['POST']
    )]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $newUser = new User();
        $newUser
            ->setName($request->get('name'))
            ->setSex($request->get('sex'))
            ->setPhone($request->get('phone'))
            ->setBirthday(new \DateTime($request->get('birthday')))
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTime());

        $entityManager->persist($newUser);
        $entityManager->flush();


        return $this->json($newUser, Response::HTTP_CREATED);
    }

    #[Route(
        path: '/{id}',
        name: 'user-update',
        methods: ['PUT']
    )]
    public function update(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, int $id): Response
    {
        // Находим пользователя по ID
        $user = $userRepository->find($id);

        if ($user === null) {
            return $this->json(['message' => 'Пользователь не найден'], Response::HTTP_NOT_FOUND);
        }
        // Получаем данные из запроса
        $data = json_decode($request->getContent(), true);


        // Обновляем поля пользователя
        $user
            ->setName($data['name'] ?? $user->getName())
            ->setPhone($data['phone'] ?? $user->getPhone())
            ->setSex($data['sex'] ?? $user->getSex())
            ->setBirthday(new \DateTime($data['birthday']) ?? $user->getBirthday());

        // Сохраняем изменения
        $entityManager->flush();

        return $this->json($user);
    }

    #[Route(
        path: '/{id}',
        name: 'user-delete',
        methods: ['DELETE']
    )]
    public function delete(int $id, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->find($id);
        if ($user === null) {
            return $this->json(['message' => 'Пользователь не найден'], Response::HTTP_NOT_FOUND);
        }
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json([], Response::HTTP_OK);
    }
}