<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = array(
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('12345678'),
                'foto' => '/img/user.jpg',
                'level' => 1
            ],
            [
                'name' => 'Ektaf Isfar Robakum',
                'email' => 'eir@gmail.com',
                'password' => bcrypt('12345678'),
                'foto' => '/img/user.jpg',
                'level' => 2
            ]
        );

        array_map(function (array $user) {
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }, $users);
    }
}
