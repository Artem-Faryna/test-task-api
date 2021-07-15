<?php
declare(strict_types=1);

namespace App\Services;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Exception\User\UserInvalidException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

class UserGrudService
{
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    private SerializerInterface $serializer;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
    ) {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->serializer = $serializer;
    }

    public function getList(array $parameters): ?array
    {
        return $this->userRepository->findByField($parameters);
    }

    /**
     * @throws UserInvalidException
     */
    public function update(User $user, string $newData): void
    {
        $newUserData = $this->serializer->deserialize($newData, User::class, 'json');
        $user->setEmail($newUserData->getEmail());
        $user->setUsername($newUserData->getUsername());
        $user->setPassword($newUserData->getPassword());

        $this->save($user);
    }

    /**
     * @throws UserInvalidException
     */
    public function create(string $userData): void
    {
        $user = $this->serializer->deserialize(
            $userData,
            User::class,
            'json',
            [AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true],
        );

        $this->save($user);
    }

    /**
     * @throws UserInvalidException
     */
    private function save(User $user): void
    {
        $errors = $this->validate($user);

        if ($errors) {
            throw new UserInvalidException($errors);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    private function validate(User $user): string
    {
        $errors = $this->validator->validate($user);
        $message = '';

        foreach ($errors as $error) {
            /** @var $error ConstraintViolation */
            $message .= $error->getMessage();
        }

        return $message;
    }
}
