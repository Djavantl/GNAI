<x-table.table :headers="['Nome do Curso', 'Disciplinas', 'Status', 'Ações']">
@foreach($courses as $course)
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
            </x-table.actions>
        </x-table.td>
    </tr>
@endforeach
</x-table.table>