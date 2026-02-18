{{-- DADOS GERAIS --}}
<section id="dados-gerais" class="mb-5 bg-soft-info rounded shadow-sm">
    
    <x-forms.section title="Dados Pessoais"  />
    <div class="pb-3 ps-3 pe-3">

        <div class="row g-3">
            <x-show.info-item label="Nome Completo" column="col-md-8" isBox="true">
                <span class="fw-bold">{{ $student->person->name }}</span>
            </x-show.info-item>
            <x-show.info-item label="Gênero" column="col-md-4" isBox="true">
                {{ ['male' => 'Masculino', 'female' => 'Feminino', 'other' => 'Outro'][$student->person->gender] ?? 'Não informado' }}
            </x-show.info-item>
            <x-show.info-item label="CPF" column="col-md-4" isBox="true">{{ $student->person->document ?? '---' }}</x-show.info-item>
            <x-show.info-item label="Nascimento" column="col-md-4" isBox="true">
                {{ $student->person->birth_date ? \Carbon\Carbon::parse($student->person->birth_date)->format('d/m/Y') : '---' }}
            </x-show.info-item>
            <x-show.info-item label="Idade" column="col-md-4" isBox="true">
                {{ $student->person->birth_date ? \Carbon\Carbon::parse($student->person->birth_date)->age . ' anos' : '---' }}
            </x-show.info-item>
            <x-show.info-item label="E-mail" column="col-md-6" isBox="true">{{ $student->person->email ?? '---' }}</x-show.info-item>
            <x-show.info-item label="Telefone" column="col-md-6" isBox="true">{{ $student->person->phone ?? '---' }}</x-show.info-item>
        </div>
    </div>
</section>