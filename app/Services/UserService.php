<?php

namespace App\Services;

use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserService
{
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    public function index()
    {
        return $this->userRepository->getAll();
    }

    public function store(String $name, String $email, String $password)
    {
        return $this->userRepository->create([
            'name' => $name,
            'email' => $email,
            'hashedPassword' => Hash::make($password),
        ]);
    }

    public function show($id)
    {
        return $this->userRepository->getById($id);
    }

    public function update($id, array $data)
    {
        return $this->userRepository->update($id, [
            'name' => $data['name'],
            'email' => $data['email'],
            'hashedPassword' => Hash::make( $data['password']),
        ]);
    }

    public function delete($id)
    {
        return $this->userRepository->delete($id);
    }

    public function changePassword($id, $password)
    {
        return $this->userRepository->changePassword($id, Hash::make($password));
    }
}
