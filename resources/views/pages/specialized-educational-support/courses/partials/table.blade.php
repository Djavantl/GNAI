<x-table.table :headers="['Nome do Curso', 'Disciplinas', 'Status', 'Ações']">
@forelse($courses as $course)
    <tr>
        <x-table.td>{{ $course->name }}</x-table.td>
        <x-table.td>{{ $course->disciplines_count }} matérias</x-table.td>
        <x-table.td>
            <span class="text-{{ $course->is_active ? 'success' : 'danger' }} fw-bold">
                {{ $course->is_active ? 'Ativo' : 'Inativo' }}
            </span>
        </x-table.td>
        <x-table.td>
            <x-table.actions>
                <x-buttons.link-button :href="route('specialized-educational-support.courses.show', $course)" variant="info"><i class="fas fa-eye"></i>Ver</x-buttons.link-button>
                <form
                    action="{{ route('specialized-educational-support.courses.destroy', $course) }}"
                    method="POST"
                    onsubmit="return confirm('Excluir este curso?')">
                    @csrf
                    @method('DELETE')

                    <x-buttons.submit-button variant="danger">
                       <i class="fas fa-trash"></i>  Excluir
                    </x-buttons.submit-button>
                </form>
            </x-table.actions>
        </x-table.td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="text-center text-muted py-5">
            <i class="fas fa-folder-open d-block mb-2" style="font-size: 2.5rem;"></i>
            Nenhum Curso encontrado.
        </td>
    </tr>
@endforelse
</x-table.table>