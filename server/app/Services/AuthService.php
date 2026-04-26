<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(array $data): array
    {
        $user = User::create([
            'firstName' => $data['firstName'],
            'lastName'  => $data['lastName'],
            'email'     => $data['email'],
            'password'  => $data['password'],
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return ['user' => array_merge($user->toArray(), ['role' => 'admin', 'profile' => null]), 'token' => $token];
    }

    public function login(array $data): array
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ])->status(401);
        }

        // Detect role by checking related tables
        $role    = 'admin';
        $profile = null;

        $faculty = DB::table('faculty')->where('userId', $user->id)->first();
        if ($faculty) {
            $role    = 'faculty';
            $profile = $faculty;
        } else {
            $student = DB::table('student')->where('userId', $user->id)->first();
            if ($student) {
                $role    = 'student';
                $profile = $student;
            }
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user'  => array_merge($user->toArray(), [
                'role'    => $role,
                'profile' => $profile,
            ]),
            'token' => $token,
        ];
    }

    public function logout($request): void
    {
        $request->user()->currentAccessToken()->delete();
    }
}