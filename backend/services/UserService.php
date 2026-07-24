<?php

declare(strict_types=1);

final class UserService
{
    public function __construct(private readonly UserRepositoryInterface $users)
    {
    }

    public function list(): array
    {
        return $this->users->findAll();
    }

    public function find(int $id): UserModel
    {
        $user = $this->users->findById($id);

        if ($user === null) {
            throw NotFoundException::resource('User');
        }

        return $user;
    }

    public function register(array $data): UserModel
    {
        $this->assertEmailIsFree($data['email']);

        return $this->users->create(new UserModel(
            null,
            $data['email'],
            $this->hash($data['password']),
            $data['name'],
            $data['notifyEmail'],
            $data['notifyPush']
        ));
    }

    public function update(int $id, array $changes): UserModel
    {
        $this->find($id);

        if (array_key_exists('email', $changes)) {
            $this->assertEmailIsFree($changes['email'], $id);
        }

        if (array_key_exists('password', $changes)) {
            $changes['passwordHash'] = $this->hash($changes['password']);
        }

        $this->users->update($id, $changes);

        return $this->find($id);
    }

    public function delete(int $id): void
    {
        if (!$this->users->delete($id)) {
            throw NotFoundException::resource('User');
        }
    }

    private function assertEmailIsFree(string $email, ?int $exceptId = null): void
    {
        if ($this->users->emailExists($email, $exceptId)) {
            throw new ConflictException('This email address is already registered.');
        }
    }

    private function hash(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
