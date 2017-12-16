<?php

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
        factory(App\User::class, 3)->create()->each(function ($u) {
            $u->transactions()->saveMany(factory(App\Transaction::class, 3)->make());
        });
    }
}
