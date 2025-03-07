<?php

namespace App\Repositories\User;

use App\Repositories\Interfaces\BaseRepositoryInterface;

interface UserRepositoryInterface extends BaseRepositoryInterface {
    public function changePassword($id, String $hashedPassword);
}
