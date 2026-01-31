<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Empréstimos - GNAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-7xl mx-auto">

    {{-- Cabeçalho --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Empréstimos de Recursos</h1>
            <p class="text-gray-600">Controle de saídas e devoluções de tecnologias e materiais pedagógicos.</p>
        </div>

        <a href="{{ route('inclusive-radar.loans.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow transition font-bold flex items-center gap-2">
            <i class="fas fa-handshake"></i>
            Novo Empréstimo
        </a>
    </div>

    {{-- Feedback de Sucesso --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-200 text-green-800 p-4 rounded mb-6 flex items-center gap-3 shadow-sm">
            <i class="fas fa-check-circle text-green-600"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Tabela Principal --}}
    <div class="bg-white p-6 rounded shadow border-t-4 border-blue-600">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                <tr class="border-b-2 border-gray-100">
                    <th class="py-3 px-4 font-bold text-gray-700">Recurso / Item</th>
                    <th class="py-3 px-4 font-bold text-gray-700">Beneficiário (Estudante)</th>
                    <th class="py-3 px-4 font-bold text-gray-700 text-center">Data Saída</th>
                    <th class="py-3 px-4 font-bold text-gray-700 text-center">Prazo Entrega</th>
                    <th class="py-3 px-4 font-bold text-gray-700 text-center">Status</th>
                    <th class="py-3 px-4 font-bold text-gray-700 text-right">Ações</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                @forelse($loans as $loan)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-4 align-middle">
                            <div class="flex items-center gap-3">
                                <div class="bg-blue-100 text-blue-600 p-2 rounded text-lg">
                                    <i class="fas {{ $loan->loanable_type === 'App\Models\InclusiveRadar\AssistiveTechnology' ? 'fa-microchip' : 'fa-book' }}"></i>
                                </div>
                                <div>
                                    {{-- Lógica Polimórfica: Tenta name (Tecnologia) ou title (Material) --}}
                                    <span class="font-bold text-gray-900 block">
                                        {{ $loan->loanable->name ?? ($loan->loanable->title ?? ($loan->loanable->description ?? 'Item Removido')) }}
                                    </span>
                                    <span class="text-[10px] text-gray-400 uppercase font-bold">
                                        {{ $loan->loanable_type === 'App\Models\InclusiveRadar\AssistiveTechnology' ? 'Tecnologia' : 'Material' }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td class="py-4 px-4 align-middle">
                            <span class="text-sm font-semibold text-gray-700 block">{{ $loan->student->person->name }}</span>
                            <span class="text-xs text-gray-500 italic">Matrícula: {{ $loan->student->registration }}</span>
                        </td>

                        <td class="py-4 px-4 text-center align-middle text-sm text-gray-600">
                            {{ $loan->loan_date->format('d/m/Y H:i') }}
                        </td>

                        <td class="py-4 px-4 text-center align-middle text-sm">
                            <span class="{{ $loan->status === 'active' && $loan->due_date->isPast() ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                                {{ $loan->due_date->format('d/m/Y') }}
                            </span>
                        </td>

                        <td class="py-4 px-4 text-center align-middle">
                            @if($loan->status === 'active')
                                @if($loan->due_date->isPast())
                                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-[11px] font-bold border border-red-200 animate-pulse">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> EM ATRASO
                                    </span>
                                @else
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[11px] font-bold border border-green-200">
                                        ATIVO
                                    </span>
                                @endif
                            @elseif($loan->status === 'late')
                                <span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-[11px] font-bold border border-amber-200">
                                    <i class="fas fa-history mr-1"></i> DEVOLVIDO (ATRASO)
                                </span>
                            @elseif($loan->status === 'returned')
                                <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-[11px] font-bold border border-gray-200">
                                    DEVOLVIDO
                                </span>
                            @elseif($loan->status === 'damaged')
                                <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-[11px] font-bold border border-orange-200">
                                    COM AVARIA
                                </span>
                            @endif
                        </td>

                        <td class="py-4 px-4 text-right align-middle">
                            <div class="flex justify-end gap-2">
                                @if($loan->status === 'active')
                                    <form action="{{ route('inclusive-radar.loans.return', $loan) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" onclick="return confirm('Confirmar a devolução?')"
                                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded shadow-sm text-xs font-bold transition flex items-center gap-1">
                                            <i class="fas fa-check"></i> DEVOLVER
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('inclusive-radar.loans.edit', $loan) }}"
                                   class="text-blue-600 hover:bg-blue-50 px-3 py-1 rounded transition border border-blue-100 text-sm font-semibold">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('inclusive-radar.loans.destroy', $loan) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Excluir este registro?')"
                                            class="text-red-400 hover:text-red-600 px-2 py-1 transition text-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-gray-400">
                            <i class="fas fa-exchange-alt text-4xl mb-4 block opacity-20"></i>
                            <p class="text-lg">Nenhum empréstimo registrado.</p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Cards de Resumo --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
        <div class="bg-white p-4 rounded shadow border-l-4 border-green-500">
            <span class="text-xs font-bold text-gray-400 uppercase">Em Uso</span>
            <p class="text-2xl font-bold text-gray-800">{{ $loans->where('status', 'active')->count() }} Recursos</p>
        </div>
        <div class="bg-white p-4 rounded shadow border-l-4 border-red-500">
            <span class="text-xs font-bold text-gray-400 uppercase">Pendentes (Atrasados)</span>
            <p class="text-2xl font-bold text-red-600">
                {{ $loans->where('status', 'active')->filter(fn($l) => $l->due_date->isPast())->count() }} Itens
            </p>
        </div>
        <div class="bg-white p-4 rounded shadow border-l-4 border-amber-500">
            <span class="text-xs font-bold text-gray-400 uppercase">Total Devolvido com Atraso</span>
            <p class="text-2xl font-bold text-amber-600">
                {{ $loans->where('status', 'late')->count() }} Registros
            </p>
        </div>
    </div>
</div>

</body>
</html>
