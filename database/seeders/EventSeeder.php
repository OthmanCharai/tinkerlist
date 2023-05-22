<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = Event::all(); // get all event
        $users = User::all()->toArray(); // get all users

        $events->map(function ($event) use ($users) {
            // associate one event with 3 random invitees
            $event->invitees()->syncWithoutDetaching(array_rand($users, 4));
        });
    }
}
