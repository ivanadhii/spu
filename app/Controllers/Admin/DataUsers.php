<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use Myth\Auth\Models\UserModel;
use Myth\Auth\Models\GroupModel;
use CodeIgniter\I18n\Time;

class DataUsers extends Controller
{
public function index()
    {
        $data['title'] = 'Data User';

        $userModel = new UserModel();
        $groupModel = new GroupModel();

        $users = $userModel->orderBy('id', 'ASC')->findAll();

        foreach ($users as &$user) {
            $group = $groupModel->getGroupsForUser($user->id);
            $user->role = $group ? $group[0]['name'] : 'User';
        }

        $data['users'] = $users;

        return view('admin/data-users', $data);
    }

    public function addRole()
    {
        $data = $this->request->getJSON();

        if (isset($data->userId) && isset($data->role)) {
            $userId = $data->userId;
            $roleName = $data->role;

            $userModel = new \Myth\Auth\Models\UserModel();
            $authGroupsModel = new \Myth\Auth\Authorization\GroupModel();

            $user = $userModel->find($userId);

            if (!$user) {
                log_message('error', 'User not found');
                return $this->response->setJSON(['success' => false, 'message' => 'User not found']);
            }

            $groupId = ($roleName === 'admin') ? 1 : 2;

            $authGroupsModel->removeUserFromAllGroups($userId);

            if ($authGroupsModel->addUserToGroup($userId, $groupId)) {
                return $this->response->setJSON(['success' => true]);
            } else {
                return $this->response->setJSON(['success' => false]);
            }
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid data format or missing properties']);
        }
    }
    public function deleteUser()
    {
        $data = $this->request->getJSON();
        $userId = $data->userId;

        $userModel = new \Myth\Auth\Models\UserModel();

        if ($userModel->delete($userId, true)) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false]);
        }
    }
}
