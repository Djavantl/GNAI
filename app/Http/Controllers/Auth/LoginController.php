<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Priority;
use App\Domains\InclusiveRadar\Domain\Enums\BarrierStatus;
use App\Domains\InclusiveRadar\Domain\Enums\WaitlistStatus;
use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\Models\Barrier;
use App\Domains\InclusiveRadar\Domain\Models\Loan;
use App\Domains\InclusiveRadar\Domain\Models\Waitlist;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SpecializedEducationalSupport\Pendency;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\Session;
use App\Models\SpecializedEducationalSupport\Pei;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Models\SpecializedEducationalSupport\Course;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->professional) {

                if ($user->professional->status === 'inactive') {

                    Auth::logout();

                    return back()->withErrors([
                        'email' => 'Este profissional está inativo e não pode acessar o sistema.'
                    ]);
                }

            }

            if ($user->is_admin || $user->professional_id || $user->teacher_id) {
                return redirect()->route('dashboard')
                    ->with('success', 'Login realizado com sucesso.');
            }

            Auth::logout();
            return back()->with('error', 'Usuário sem permissão de acesso.');
        }

        return back()->withErrors([
            'email' => 'Credenciais inválidas.'
        ]);
    }

    public function index()
    {
        // --- AEE ---
        $totalStudents = Student::count();
        $totalSessions = Session::count();
        $totalPeis = Pei::count();
        $totalProfessionals = Professional::count();
        $totalCourses = Course::count();
        $totalPeisFinished = Pei::where('is_finished', true)->count();
        $totalPeisNotFinished = Pei::where('is_finished', false)->count();
        $totalPendingPendencies = Pendency::pending()->count();
        $totalOverduePendencies = Pendency::pending()
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', today())
            ->count();
        $pendenciesByPriority = collect(Priority::cases())->map(function (Priority $priority) {
            return [
                'label' => $priority->label(),
                'color' => $priority->color(),
                'count' => Pendency::pending()->where('priority', $priority->value)->count(),
            ];
        })->values();
        $sessionsByStatus = collect(Session::statusOptions())->map(function (string $label, string $status) {
            return [
                'label' => $label,
                'count' => Session::where('status', $status)->count(),
            ];
        })->values();

        // --- Radar Inclusivo ---
        $totalAt = AssistiveTechnology::query()->where('is_active', true)->count();
        $totalAem = AccessibleEducationalMaterial::query()->where('is_active', true)->count();
        $totalLoans = Loan::count();
        $totalWaitingAndNotified = Waitlist::whereIn('status', [
            WaitlistStatus::WAITING->value,
            WaitlistStatus::NOTIFIED->value,
        ])->count();
        $totalBarriers = Barrier::count();

        $barrierStatusCounts = collect(BarrierStatus::cases())->map(function (BarrierStatus $status) {
            return [
                'label' => $status->label(),
                'color' => $status->color(),
                'count' => Barrier::query()
                    ->get()
                    ->filter(fn (Barrier $barrier) => $barrier->latestStatus() === $status)
                    ->count(),
            ];
        })->filter(fn ($item) => $item['count'] > 0)->values();

        $mapBarriers = Barrier::with(['category', 'location', 'institution', 'inspections'])
            ->get()
            ->map(function (Barrier $barrier) {
                $currentStatus = $barrier->latestStatus();

                if (!$currentStatus) {
                    return null;
                }

                return [
                    'id' => $barrier->id,
                    'name' => $barrier->name,
                    'lat' => (float) $barrier->latitude,
                    'lng' => (float) $barrier->longitude,
                    'status' => $currentStatus->value,
                    'status_label' => $currentStatus->label(),
                    'blocks_map' => (bool) ($barrier->category?->blocks_map ?? false),
                    'category_name' => $barrier->category?->name ?? 'Sem Categoria',
                    'color' => $currentStatus->color(),
                    'url' => route('inclusive-radar.barriers.show', $barrier),
                ];
            })
            ->filter()
            ->values();

        return view('pages.dashboard', [
            // AEE
            'totalStudents' => $totalStudents,
            'totalSessions' => $totalSessions,
            'totalPeis' => $totalPeis,
            'totalProfessionals' => $totalProfessionals,
            'totalPeisFinished' => $totalPeisFinished,
            'totalPeisNotFinished' => $totalPeisNotFinished,
            'totalCourses' => $totalCourses,
            'totalPendingPendencies' => $totalPendingPendencies,
            'totalOverduePendencies' => $totalOverduePendencies,
            'pendenciesByPriority' => $pendenciesByPriority,
            'sessionsByStatus' => $sessionsByStatus,

            // Radar Inclusivo
            'totalAt' => $totalAt,
            'totalAem' => $totalAem,
            'totalLoans' => $totalLoans,
            'totalWaiting' => $totalWaitingAndNotified,
            'totalBarriers' => $totalBarriers,
            'barrierStatusCounts' => $barrierStatusCounts,
            'mapBarriers' => $mapBarriers,
        ]);
    }

    public function logout(Request $request)
    {
        session()->forget('impersonator_id');

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
