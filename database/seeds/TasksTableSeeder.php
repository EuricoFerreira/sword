<?php

use App\Task;
use Illuminate\Database\Seeder;

class TasksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Truncate our existing records
        Task::truncate();

        $faker = \Faker\Factory::create();

        for($i = 0; $i < 20; $i++) {
            Task::create([
                'summary' => $faker->sentence(),
                'completed_at' => $faker->dateTime()

            ]);
        }
    }
}
