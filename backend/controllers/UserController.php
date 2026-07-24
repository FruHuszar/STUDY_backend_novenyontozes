<?php

declare(strict_types=1);

final class UserController
{
    private const MAX_NAME_LENGTH = 100;

    public function __construct(private readonly UserService $users)
    {
    }

    public function index(Request $request): Response
    {
        return Response::json(200, $this->users->list());
    }

    public function show(Request $request): Response
    {
        return Response::json(200, $this->users->find($request->id()));
    }

    public function store(Request $request): Response
    {
        $validator = $request->validator();

        $data = [
            'email' => $validator->requiredEmail('email'),
            'password' => $validator->requiredPassword('password'),
            'name' => $validator->requiredString('name', self::MAX_NAME_LENGTH),
            'notifyEmail' => $validator->boolean('notifyEmail', true),
            'notifyPush' => $validator->boolean('notifyPush', false),
        ];

        $validator->assertValid();

        $user = $this->users->register($data);

        return Response::created('/api/users/' . $user->getId(), $user);
    }

    public function update(Request $request): Response
    {
        $validator = $request->validator();
        $changes = [];

        if ($validator->has('email')) {
            $changes['email'] = $validator->requiredEmail('email');
        }

        if ($validator->has('password')) {
            $changes['password'] = $validator->requiredPassword('password');
        }

        if ($validator->has('name')) {
            $changes['name'] = $validator->requiredString('name', self::MAX_NAME_LENGTH);
        }

        if ($validator->has('notifyEmail')) {
            $changes['notifyEmail'] = $validator->boolean('notifyEmail') ? 1 : 0;
        }

        if ($validator->has('notifyPush')) {
            $changes['notifyPush'] = $validator->boolean('notifyPush') ? 1 : 0;
        }

        $validator->assertValid();

        return Response::json(200, $this->users->update($request->id(), $changes));
    }

    public function destroy(Request $request): Response
    {
        $this->users->delete($request->id());

        return Response::noContent();
    }
}
