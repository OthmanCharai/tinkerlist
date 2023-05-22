<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // interact with developers
        $userNumber = (int) $this->command->ask('how many user you want?', '10');
        $eventNumberForUser = (int) $this->command->ask('how many events you want for these user?', '10');

        //create $userNumber user.
        User::factory()
            // foreach user create and associate  $eventNumber Event.
            ->has(
                Event::factory()
                    ->count($eventNumberForUser)
            )
            ->count($userNumber)
            ->create();
    }
}
