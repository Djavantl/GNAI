<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar Empréstimo - GNAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow border-t-4 border-blue-600">
    <h1 class="text-2xl font-bold mb-6 border-b pb-4 text-gray-800">Registrar Novo Empréstimo</h1>

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

    <form action="{{ route('inclusive-radar.loans.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 gap-6">

            {{-- Seção do Item (Polimórfico) --}}
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-blue-900 mb-1">Tipo de Recurso *</label>
                    <select name="loanable_type" id="loanable_type" class="w-full border p-2 rounded bg-white focus:ring-2 focus:ring-blue-500" required>
                        <option value="">-- Selecione o tipo --</option>
                        <option value="App\Models\InclusiveRadar\AssistiveTechnology" {{ old('loanable_type') == 'App\Models\InclusiveRadar\AssistiveTechnology' ? 'selected' : '' }}>Tecnologia Assistiva</option>
                        <option value="App\Models\InclusiveRadar\AccessibleEducationalMaterial" {{ old('loanable_type') == 'App\Models\InclusiveRadar\AccessibleEducationalMaterial' ? 'selected' : '' }}>Material Pedagógico</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-blue-900 mb-1">Item Específico *</label>
                    <select name="loanable_id" id="loanable_id" class="w-full border p-2 rounded bg-white focus:ring-2 focus:ring-blue-500" required>
                        <option value="">-- Selecione o tipo primeiro --</option>
                    </select>
                </div>
            </div>

            {{-- Seção de Pessoas --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Estudante Beneficiário *</label>
                    <select name="student_id" class="w-full border p-2 rounded bg-white focus:ring-2 focus:ring-green-500">
                        <option value="">-- Selecione o estudante --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->person->name }} ({{ $student->registration }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Profissional Responsável *</label>
                    <select name="professional_id" class="w-full border p-2 rounded bg-white focus:ring-2 focus:ring-green-500">
                        <option value="">-- Selecione o profissional --</option>
                        @foreach($professionals as $prof)
                            <option value="{{ $prof->id }}" {{ old('professional_id') == $prof->id ? 'selected' : '' }}>
                                {{ $prof->person->name }} - {{ $prof->registration }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Datas --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded border border-gray-200">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Data do Empréstimo *</label>
                    <input type="datetime-local" name="loan_date" value="{{ old('loan_date', now()->format('Y-m-d\TH:i')) }}"
                           class="w-full border p-2 rounded focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Previsão de Devolução *</label>
                    <input type="date" name="due_date" value="{{ old('due_date') }}"
                           class="w-full border p-2 rounded focus:ring-2 focus:ring-green-500">
                    <p class="text-xs text-gray-500 mt-1 italic">Defina o prazo limite para a entrega do item.</p>
                </div>
            </div>

            {{-- Observações --}}
            <div>
                <label class="block font-bold text-gray-700 mb-1">Observações / Estado do Item</label>
                <textarea name="observation" rows="3" class="w-full border p-2 rounded focus:ring-2 focus:ring-green-500"
                          placeholder="Anote detalhes sobre o estado de conservação no momento da entrega...">{{ old('observation') }}</textarea>
            </div>

            {{-- Botões --}}
            <div class="flex gap-4 mt-4 border-t pt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-10 py-3 rounded shadow-lg transition font-bold text-lg flex-1 md:flex-none">
                    <i class="fas fa-handshake mr-2"></i> Confirmar Empréstimo
                </button>
                <a href="{{ route('inclusive-radar.loans.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded transition flex items-center font-bold">
                    Cancelar
                </a>
            </div>
        </div>
    </form>
</div>

<script>
    const typeSelect = document.getElementById('loanable_type');
    const itemSelect = document.getElementById('loanable_id');

    // Mapeamento dos itens vindo do servidor
    const items = {
        'App\\Models\\InclusiveRadar\\AssistiveTechnology': @json($assistive_technologies ?? []),
        'App\\Models\\InclusiveRadar\\AccessibleEducationalMaterial': @json($educational_materials ?? [])
    };

    typeSelect.addEventListener('change', function() {
        const selectedType = this.value;
        itemSelect.innerHTML = '<option value="">-- Selecione o item --</option>';

        if (items[selectedType]) {
            items[selectedType].forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;

                // Lógica Inteligente para o nome do item:
                // Tenta 'name' (Tecnologia), senão 'title' ou 'description' (Material)
                const displayName = item.name || item.title || item.description || 'Item sem identificação';
                const assetCode = item.asset_code || 'S/N';

                option.text = `${displayName} (${assetCode})`;
                itemSelect.appendChild(option);
            });
        }
    });

    // Re-popular se houver erro de validação (old values)
    window.addEventListener('DOMContentLoaded', () => {
        if (typeSelect.value) {
            typeSelect.dispatchEvent(new Event('change'));
            setTimeout(() => {
                itemSelect.value = "{{ old('loanable_id') }}";
            }, 10);
        }
    });
</script>
</body>
</html>
