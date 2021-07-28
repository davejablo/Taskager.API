<?php

use App\Project;
use App\User;
use App\UserProfile;
use Illuminate\Database\Seeder;

class UserProfileSeeder extends Seeder
{
    private $projects;
    private $current;
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function __construct()
    {
        $this->projects = Project::all()->pluck('id');
        $this->current = 0;
    }

    public function run()
    {
        UserProfile::truncate();
        User::truncate();

        factory(App\User::class, 1)->create()->each(function ($user) {
            $user->profile()->save(factory(App\UserProfile::class)->make());
            $user->roles()->attach(1);
        });

        foreach ($this->projects as $project){
            $this->current = $project;

            factory(App\User::class, 1)->create()->each(function ($user) {
                $user->profile()->save(factory(App\UserProfile::class)->make());
                $user->roles()->attach(2);
                $user->project()->associate($this->current);
                $user->save();
            });

            factory(App\User::class, 1)->create()->each(function ($user) {
                $user->profile()->save(factory(App\UserProfile::class)->make());
                $user->roles()->attach(4);
                $user->project()->associate($this->current);
                $user->save();
            });

            factory(App\User::class, 4)->create()->each(function ($user) {
                $user->profile()->save(factory(App\UserProfile::class)->make());
                $user->roles()->attach(3);
                $user->project()->associate($this->current);
                $user->save();
            });
        }
    }
}