<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SpecializedEducationalSupport\PeiEvaluationRequest;
use App\Enums\SpecializedEducationalSupport\EvaluationType;
use App\Models\SpecializedEducationalSupport\Pei;
use App\Models\SpecializedEducationalSupport\PeiEvaluation;
use App\Models\SpecializedEducationalSupport\Semester;
use App\Services\SpecializedEducationalSupport\PeiEvaluationService;

class PeiEvaluationController extends Controller
{   
    protected PeiEvaluationService $service;

    public function __construct(PeiEvaluationService $service)
    {
        $this->service = $service;
    }

    public function index(Pei $pei)
    {
        $pei_evaluations = $this->service->index($pei);
        return view('pages.specialized-educational-support.pei-evaluations.index', compact('pei', 'pei_evaluations'));
    }

    public function show(PeiEvaluation $pei_evaluation)
    {
        $pei_evaluation = $this->service->show($pei_evaluation);
        $pei = $pei_evaluation->pei;
        $types = EvaluationType::cases();

        return view('pages.specialized-educational-support.pei-evaluations.show', compact('pei_evaluation', 'pei', 'types'));
    }

    public function create(Pei $pei)
    {
        $semester = Semester::current();
        
        return view('pages.specialized-educational-support.pei-evaluations.create', compact('semester', 'pei'));
    }

    public function store(Pei $pei, PeiEvaluationRequest $request)
    {
        try {
            $pei_evaluation = $this->service->create($pei, $request->validated());

            return redirect()
                ->route('specialized-educational-support.pei-evaluation.show', $pei_evaluation)
                ->with('success', 'Avaliação registrada com sucesso.');

        } catch (\DomainException $e) {
            return back()
                ->withInput()
                ->withErrors(['evaluation' => $e->getMessage()]);
        }
    }

    public function edit(PeiEvaluation $pei_evaluation)
    {
        $pei = $pei_evaluation->pei;

        return view('pages.specialized-educational-support.pei-evaluations.edit', compact('pei_evaluation', 'pei'));
    }

    public function update(PeiEvaluation $pei_evaluation, PeiEvaluationRequest $request)
    {
        $this->service->update($pei_evaluation, $request->validated());

        return redirect()
            ->route('specialized-educational-support.pei-evaluation.show', $pei_evaluation)
            ->with('success', 'Dados da avaliação do PEI atualizados com sucesso.');
    }

    public function destroy(PeiEvaluation $pei_evaluation)
    {
        $pei = $pei_evaluation->pei;
        $this->service->delete($pei_evaluation);

        return redirect()
            ->route('specialized-educational-support.pei-evaluation.index', $pei)
            ->with('success', 'Avaliação do PEI removida com sucesso.');
    }

    public function generatePdf(PeiEvaluation $pei_evaluation) 
    {
        // Agora o Laravel vai buscar automaticamente a avaliação no banco pelo ID da URL
        $pei_evaluation->load([
            'pei.student.person',
            'pei.course',
            'pei.discipline',
            'pei.semester',
            'professional.person'
        ]);

        if (!$pei_evaluation->pei) {
            abort(404, 'Plano PEI não encontrado para esta avaliação.');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'pages.specialized-educational-support.pei-evaluations.pdf',
            ['evaluation' => $pei_evaluation] // Passamos para a view com o nome que ela espera
        );

        return $pdf->stream(
            "Avaliacao_PEI_{$pei_evaluation->pei->student->person->name}.pdf"
        );
    }
}
