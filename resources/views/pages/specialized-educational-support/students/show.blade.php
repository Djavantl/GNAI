@extends('layouts.app')

@section('content')
<div class="mb-4">
    <x-breadcrumb :items="[
        'Home' => route('dashboard'),
        'Alunos' => route('specialized-educational-support.students.index'),
        $student->person->name => null
    ]" />
</div>

<div class="d-flex justify-content-between mb-3">
    <div>
        <h2 class="text-title">Prontuário de {{$student->person->name}}</h2>
        <p class="text-muted">Visualize o ecossistema completo do aluno: desde informações cadastrais até o histórico detalhado de interações, atendimentos e evoluções no sistema.</p>
    </div>
    <x-buttons.link-button :href="route('specialized-educational-support.students.index')" variant="secondary">
        <i class="fas fa-arrow-left "></i> Voltar 
    </x-buttons.link-button>
</div>

<div class="card shadow-sm border-0">
    <div class="row g-0">

        @include('pages.specialized-educational-support.students.record.sidebar')

        <div class="col-md-9 p-0">
            <div class="p-4 overflow-auto" style="max-height: 85vh;">

                @include('pages.specialized-educational-support.students.record.personal-data')
                @include('pages.specialized-educational-support.students.record.academic-info')
                @include('pages.specialized-educational-support.students.record.deficiencies')
                @include('pages.specialized-educational-support.students.record.guardians')
                @include('pages.specialized-educational-support.students.record.contexts')
                @include('pages.specialized-educational-support.students.record.peis')
                @include('pages.specialized-educational-support.students.record.documents')
                @include('pages.specialized-educational-support.students.record.sessions')

            </div>

            <div class="bg-white p-3 border-top d-flex justify-content-center align-items-center no-print shadow-lg">
                <span class="small text-muted">Sistema GNAI 2026</span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .bg-soft-info { background-color: #eef0f3; }
    .list-group-item.active { background-color: #4D44B5; border-color: #4D44B5; }
    .transition-all { transition: all 0.3s ease; }
    .hover-shadow:hover { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
    #student-menu .list-group-item { border-left: 3px solid transparent; }
    
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const links = document.querySelectorAll('#student-menu a');
        const sections = Array.from(links).map(l => document.querySelector(l.getAttribute('href'))).filter(Boolean);

        function activateLink() {
            let index = sections.length - 1;
            for (let i = 0; i < sections.length; i++) {
                const rect = sections[i].getBoundingClientRect();
                if (rect.top > 150) break;
                index = i;
            }
            links.forEach(l => l.classList.remove('active'));
            if (links[index]) links[index].classList.add('active');
        }

        links.forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        const container = document.querySelector('.overflow-auto');
        container.addEventListener('scroll', activateLink);
        activateLink();
    });
</script>
@endpush