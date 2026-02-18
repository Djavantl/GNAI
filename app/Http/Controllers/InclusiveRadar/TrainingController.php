<?php

namespace App\Http\Controllers\InclusiveRadar;

use App\Http\Controllers\Controller;
use App\Http\Requests\InclusiveRadar\TrainingRequest;
use App\Models\InclusiveRadar\Training;
use App\Models\InclusiveRadar\TrainingFile;
use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Services\InclusiveRadar\TrainingService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TrainingController extends Controller
{
    public function __construct(
        protected TrainingService $service
    ) {}

    public function index(Request $request): View
    {
        $title = trim($request->title ?? '');

        $trainings = Training::query()
            ->with(['trainable'])
            ->searchTitle($title ?: null)
            ->when($request->filled('is_active'), function ($query) use ($request) {
                $query->active($request->is_active);
            })
            ->orderBy('title')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('pages.inclusive-radar.trainings.partials.table', compact('trainings'));
        }

        return view('pages.inclusive-radar.trainings.index', compact('trainings'));
    }

    // No TrainingController.php

    public function create(Request $request): View
    {
        $technologies = AssistiveTechnology::active(true)->orderBy('name')->get();
        $materials = AccessibleEducationalMaterial::active(true)->orderBy('name')->get();

        $preSelectedType = $request->query('type');
        $preSelectedId = $request->query('id');

        return view('pages.inclusive-radar.trainings.create', compact(
            'technologies',
            'materials',
            'preSelectedType',
            'preSelectedId'
        ));
    }

    public function store(TrainingRequest $request): RedirectResponse
    {
        $this->service->store($request->validated());

        return redirect()
            ->route('inclusive-radar.trainings.index')
            ->with('success', 'Treinamento criado com sucesso!');
    }

    public function show(Training $training): View
    {
        $training->load([
            'trainable',
            'files',
        ]);

        return view(
            'pages.inclusive-radar.trainings.show',
            compact('training')
        );
    }

    public function edit(Training $training): View
    {
        $training->load([
            'trainable',
            'files',
        ]);

        $technologies = AssistiveTechnology::active(true)->orderBy('name')->get();
        $materials = AccessibleEducationalMaterial::active(true)->orderBy('name')->get();

        return view(
            'pages.inclusive-radar.trainings.edit',
            compact('training', 'technologies', 'materials')
        );
    }

    public function update(TrainingRequest $request, Training $training): RedirectResponse
    {
        $this->service->update($training, $request->validated());

        return redirect()
            ->route('inclusive-radar.trainings.index')
            ->with('success', 'Treinamento atualizado com sucesso!');
    }

    public function toggleActive(Training $training): RedirectResponse
    {
        $this->service->toggleActive($training);

        return redirect()->back()->with('success', 'Status atualizado com sucesso!');
    }

    public function destroy(Training $training): RedirectResponse
    {
        try {
            $this->service->delete($training);

            return redirect()
                ->route('inclusive-radar.trainings.index')
                ->with('success', 'Treinamento removido com sucesso!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function destroyFile(TrainingFile $file): RedirectResponse
    {
        try {
            $file->delete();

            return redirect()->back()->with('success', 'Arquivo removido com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao remover arquivo: ' . $e->getMessage());
        }
    }
}
