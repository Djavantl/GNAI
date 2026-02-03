<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Empréstimo - GNAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-100 p-8">
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow border-t-4 border-blue-600">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-800">Editar Registro de Empréstimo</h1>
        <span class="text-sm text-gray-500 font-mono bg-gray-100 px-2 py-1 rounded">Protocolo: #{{ $loan->id }}</span>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded">
            <p class="font-bold mb-1 italic">Atenção: Existem erros no preenchimento.</p>
            <ul class="list-disc ml-5 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Alerta de Status para Itens Ativos Atrasados --}}
    @if($loan->status === 'active' && $loan->due_date->isPast())
        <div class="mb-6 p-4 bg-amber-50 border-l-4 border-amber-500 text-amber-800 rounded flex items-center gap-3">
            <i class="fas fa-clock fa-spin text-xl"></i>
            <div>
                <p class="font-bold">Atenção: Este item está com a devolução atrasada!</p>
                <p class="text-sm">O prazo encerrou em {{ $loan->due_date->format('d/m/Y') }}.</p>
            </div>
        </div>
    @endif

    <form action="{{ route('inclusive-radar.loans.update', $loan->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- CAMPOS OCULTOS PARA MANTER A INTEGRIDADE NA VALIDAÇÃO --}}
        <input type="hidden" name="loanable_id" value="{{ $loan->loanable_id }}">
        <input type="hidden" name="loanable_type" value="{{ $loan->loanable_type }}">

        <div class="grid grid-cols-1 gap-6">

            {{-- Informações do Recurso (Visualmente bloqueado) --}}
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block font-bold text-gray-500 mb-2 uppercase text-xs tracking-widest">Recurso Emprestado</label>
                <div class="flex items-center gap-4">
                    <div class="bg-blue-600 text-white p-3 rounded shadow">
                        {{-- Ícone Dinâmico --}}
                        <i class="fas {{ $loan->loanable_type === 'App\Models\InclusiveRadar\AssistiveTechnology' ? 'fa-microchip' : 'fa-book' }} text-xl"></i>
                    </div>
                    <div>
                        {{-- Lógica para evitar o "undefined" no nome --}}
                        <p class="font-bold text-gray-800 text-lg">
                            {{ $loan->loanable->name ?? ($loan->loanable->title ?? ($loan->loanable->description ?? 'Item não identificado')) }}
                        </p>
                        <p class="text-sm text-gray-600 italic">
                            Patrimônio: {{ $loan->loanable->asset_code ?? 'N/A' }}
                        </p>
                    </div>
                </div>
                <p class="text-[10px] text-blue-600 font-bold mt-3">* O item não pode ser alterado após o registro inicial.</p>
            </div>

            {{-- Participantes --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Estudante *</label>
                    <select name="student_id" class="w-full border p-2 rounded bg-white focus:ring-2 focus:ring-blue-500">
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id', $loan->student_id) == $student->id ? 'selected' : '' }}>
                                {{ $student->person->name }} ({{ $student->registration }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Profissional Responsável *</label>
                    <select name="professional_id" class="w-full border p-2 rounded bg-white focus:ring-2 focus:ring-blue-500">
                        @foreach($professionals as $prof)
                            <option value="{{ $prof->id }}" {{ old('professional_id', $loan->professional_id) == $prof->id ? 'selected' : '' }}>
                                {{ $prof->person->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Datas e Prazos --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded border border-gray-200">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Data de Saída</label>
                    <input type="datetime-local" name="loan_date"
                           value="{{ old('loan_date', $loan->loan_date->format('Y-m-d\TH:i')) }}"
                           class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 bg-gray-100" readonly>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Nova Previsão de Entrega</label>
                    <input type="date" name="due_date"
                           value="{{ old('due_date', $loan->due_date->format('Y-m-d')) }}"
                           class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            {{-- Status e Observações --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Status Atual do Empréstimo</label>
                    <select name="status" class="w-full border p-2 rounded bg-white focus:ring-2 focus:ring-blue-500">
                        <option value="active" {{ old('status', $loan->status) == 'active' ? 'selected' : '' }}>Ativo (Com o aluno)</option>
                        <option value="returned" {{ old('status', $loan->status) == 'returned' ? 'selected' : '' }}>Devolvido (No prazo)</option>
                        <option value="late" {{ old('status', $loan->status) == 'late' ? 'selected' : '' }}>Devolvido (Com atraso)</option>
                        <option value="damaged" {{ old('status', $loan->status) == 'damaged' ? 'selected' : '' }}>Devolvido (Com Avaria)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Data Real da Devolução</label>
                    <input type="datetime-local" name="return_date"
                           value="{{ old('return_date', $loan->return_date ? $loan->return_date->format('Y-m-d\TH:i') : '') }}"
                           class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">Observações do Histórico</label>
                <textarea name="observation" rows="3"
                          class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500"
                          placeholder="Relate o estado do item na entrega...">{{ old('observation', $loan->observation) }}</textarea>
            </div>

            <hr class="my-4">

            <div class="flex gap-4">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-10 py-3 rounded shadow-lg transition font-bold text-lg flex-1 md:flex-none">
                    <i class="fas fa-save mr-2"></i> Atualizar Registro
                </button>
                <a href="{{ route('inclusive-radar.loans.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded transition flex items-center font-bold">
                    Voltar
                </a>
            </div>
        </div>
    </form>
</div>
</body>
</html>
