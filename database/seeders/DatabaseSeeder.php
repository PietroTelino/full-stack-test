<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\select;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       $option = select(
            label: 'Criar usuário dev@test.com com senha 123?',
            options: [
                1 => 'Sim',
                2 => 'Não',
            ]
        );

        if ($option === 1) {
            User::factory()->withoutTwoFactor()->withPersonalTeam()->create([
                'name' => 'Programador',
                'email' => 'dev@test.com',
                'password' => Hash::make('123'),
            ]);
        }
    }
}
