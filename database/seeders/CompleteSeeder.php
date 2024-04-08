<?php

namespace Database\Seeders;

use App\Models\MappingRoleTask;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class CompleteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // roles
        $roles = [
            [
                'id' => 1,
                'role_code' => 'admin',
                'role_name' => 'Admin'
            ],
        ];
        foreach ($roles as $role) Role::create($role);
        // tasks
        $tasks = ['users'];
        $permissions = ['view','show','lookup','create','update','delete'];
        foreach ($tasks as $task) {
            foreach ($permissions as $permission) {
                $createTask = Task::create([
                    'task_code' => "$permission-$task",
                    'task_name' => "$permission $task",
                    'task_group' => "$task",
                ]);
                foreach (Role::get() as $_role) {
                    MappingRoleTask::create([
                        'active' => $_role->role_code == 'super-admin' ? 1 : 0,
                        'role_id' => $_role->id,
                        'task_id' => $createTask->id
                    ]);
                }
            }
        }
        // user
        $users = [
            [
                'fullname' => 'Super Admin',
                'username' => 'super-admin',
                'password' => bcrypt('admin'),
                'email' => 'super.admin@email.com',
                'role_id' => -1,
            ],
            [
                'fullname' => 'Admin',
                'username' => 'admin',
                'password' => bcrypt('admin'),
                'email' => 'admin@email.com',
                'role_id' => 1,
            ],
            [
                'fullname' => 'Field Engineer',
                'username' => 'field-engineer',
                'password' => bcrypt('123456'),
                'email' => 'field.engineer@email.com',
                'role_id' => 2,
            ],
        ];
        foreach ($users as $user) User::create($user);
    }
}
