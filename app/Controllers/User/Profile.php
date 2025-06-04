<?php

namespace App\Controllers\User;

use CodeIgniter\Controller;
use Myth\Auth\Models\UserModel;

class Profile extends Controller
{
    public function index()
    {
        $users = new UserModel();
        $userId = user()->id;

        $data['users'] = $users->find($userId);

        return view('user/profile', $data);
    }
}
