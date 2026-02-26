<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Services\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function edit()
    {
        $user = auth()->user();

        if ($user->is_admin) {
            return redirect()->route('dashboard')
                ->with('error', 'Administradores não possuem perfil de dados pessoais para edição.');
        }

        $person = $user->professional?->person ?? $user->teacher?->person;
        $professional = $user->professional;
        $teacher = $user->teacher;

        return view('pages.profile.edit', compact('person', 'professional', 'teacher'));
    }

    public function update(ProfileRequest $request)
    {
        $user = auth()->user();

        if ($user->is_admin) {
            return redirect()->route('dashboard')
                ->with('error', 'Administradores não possuem perfil de dados pessoais para edição.');
        }

        try {
            $this->profileService->updateProfile(
                $user, 
                $request->validated(), 
                $request->file('photo')
            );

            return back()->with('success', 'Seu perfil foi atualizado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao atualizar perfil: ' . $e->getMessage());
        }
    }
}