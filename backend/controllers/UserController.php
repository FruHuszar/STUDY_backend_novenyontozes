<?php

declare(strict_types=1);

final class UserController
{
    private UserRepository $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    public function index(): void
    {
        Response::json(200, $this->users->findAll());
    }

    public function show(int $id): void
    {
        $user = $this->users->findById($id);

        if ($user === null) {
            Response::error(404, 'User not found.');

            return;
        }

        Response::json(200, $user);
    }

    public function store(Request $request): void
    {
        $missing = $this->missingFields($request, ['email', 'password', 'name']);

        if ($missing !== []) {
            Response::error(422, 'Missing required fields.', $missing);

            return;
        }

        $email = (string) $request->input('email');

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            Response::error(422, 'Invalid email address.');

            return;
        }

        if ($this->users->emailExists($email)) {
            Response::error(409, 'This email address is already registered.');

            return;
        }

        $user = $this->users->create(new UserModel(
            null,
            $email,
            password_hash((string) $request->input('password'), PASSWORD_DEFAULT),
            (string) $request->input('name'),
            (bool) $request->input('notifyEmail', true),
            (bool) $request->input('notifyPush', false)
        ));

        Response::created('/api/users/' . $user->getId(), $user);
    }

    public function update(int $id, Request $request): void
    {
        if ($this->users->findById($id) === null) {
            Response::error(404, 'User not found.');

            return;
        }

        $changes = [];

        if ($request->has('email')) {
            $email = (string) $request->input('email');

            if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                Response::error(422, 'Invalid email address.');

                return;
            }

            if ($this->users->emailExists($email, $id)) {
                Response::error(409, 'This email address is already registered.');

                return;
            }

            $changes['email'] = $email;
        }

        if ($request->has('password')) {
            $changes['password_hash'] = password_hash((string) $request->input('password'), PASSWORD_DEFAULT);
        }

        if ($request->has('name')) {
            $changes['name'] = (string) $request->input('name');
        }

        if ($request->has('notifyEmail')) {
            $changes['notify_email'] = $request->input('notifyEmail') ? 1 : 0;
        }

        if ($request->has('notifyPush')) {
            $changes['notify_push'] = $request->input('notifyPush') ? 1 : 0;
        }

        Response::json(200, $this->users->update($id, $changes));
    }

    public function destroy(int $id): void
    {
        if (!$this->users->delete($id)) {
            Response::error(404, 'User not found.');

            return;
        }

        Response::noContent();
    }

    private function missingFields(Request $request, array $required): array
    {
        $missing = [];

        foreach ($required as $field) {
            $value = $request->input($field);

            if ($value === null || $value === '') {
                $missing[] = $field;
            }
        }

        return $missing;
    }
}
