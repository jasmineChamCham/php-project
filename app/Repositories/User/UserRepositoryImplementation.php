<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;

class UserRepositoryImplementation implements UserRepositoryInterface {
    public function getAll()
    {
        return User::all();
    }

    public function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['hashedPassword'],
        ]);
    }

    public function getById($id)
    {
        return User::find($id);
    }

    public function update($id, array $data)
    {
        $user = User::findOrFail($id);
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['hashedPassword'],
        ]);
        return $user;
    }

    public function delete($id)
    {
        return User::destroy($id);
    }

    public function changePassword($id, $hashedPassword)
    {
        $user = User::findOrFail($id);
        $user->update(['password' => $hashedPassword]);
        return $user;
    }
}