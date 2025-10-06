<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            // UUID primary key (wajib untuk model kamu)
            'id'                => (string) Str::uuid(),
            'name'              => $this->faker->name(),
            'email'             => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),                 // karena route /dashboard pakai 'verified'
            'password'          => Hash::make('password'),// test pakai 'password'
            'division_id'       => null,                  // atau isi jika perlu
            'role'              => 'user',                // default role
            'remember_token'    => Str::random(10),
        ];
    }

    // state unverified kalau suatu saat dibutuhkan
    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }
}
