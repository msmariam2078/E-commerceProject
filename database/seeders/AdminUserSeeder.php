<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
           
            "name"=>"sami",
            
            "email"=>"sami@gmail.com",
            "password"=>Hash::make("1234567"),
          'is_admin'=>true
        
        ]);

    }
}
