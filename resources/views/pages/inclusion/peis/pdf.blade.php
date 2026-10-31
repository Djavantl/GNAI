<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>PEI - {{ $item->discipline->name }} - {{ $pei->student->person->name }}</title>
    <x-pdf.styles />
</head>
<body>

    <x-pdf.header
        title="Plano Educacional Individualizado (PEI)"
        :status="$pei->is_current ? 'PEI Atual' : 'Histórico'"
        :meta="[
            'Estudante' => $pei->student->person->name,
            'Componente Curricular' => mb_strtoupper($item->discipline->name, 'UTF-8'),
            'Versão' => $pei->version ?? 'Não informada',
        ]"
    />

    <table class="info-table">
        <tr>
            <td colspan="2"><span class="field-label">Nome do Estudante:</span> {{ $pei->student->person->name }}</td>
        </tr>
        <tr>
            <td class="pdf-w-60"><span class="field-label">Curso:</span> {{ $pei->course->name }}</td>
            <td class="pdf-w-40"><span class="field-label">Ano - Semestre:</span> {{ $pei->semester->label }}</td>
        </tr>
        <tr>
            <td class="pdf-w-60"><span class="field-label">Componente Curricular:</span> {{ mb_strtoupper($item->discipline->name, 'UTF-8') }}</td>
            <td class="pdf-w-40"><span class="field-label">Docente:</span> {{ $item->teacher->person->name }}</td>
        </tr>
        <tr>
            <td class="pdf-w-60"><span class="field-label">Versão do PEI:</span> {{ $pei->version ?? 'Não informada' }}</td>
            <td class="pdf-w-40"><span class="field-label">PEI Atual:</span> {{ $pei->is_current ? 'Sim' : 'Não' }}</td>
        </tr>
    </table>

    <div class="section-title">Informações de Apoio Pedagógico (NAPNE)</div>
    
    <span class="field-label">Histórico (Trajetória do Estudante):</span>
    <div class="content-box">{{ $pei->studentContext->history }}</div>

    <span class="field-label">Necessidades Educacionais Específicas:</span>
    <div class="content-box">{{ $pei->studentContext->specific_educational_needs }}</div>

    <span class="field-label">Conhecimentos e Interesses:</span>
    <div class="content-box">{{ $pei->studentContext->knowledge }}</div>

    <span class="field-label">Dificuldades Apresentadas:</span>
    <div class="content-box">{{ $pei->studentContext->difficulties }}</div>

    <div class="section-title">Adaptações Razoáveis e/ou Acessibilidades Curriculares</div>

    <span class="field-label">Objetivos Específicos:</span>
    <div class="content-box">{!! \App\Shared\Infrastructure\Security\RichTextSanitizer::sanitize((string) $item->specific_objectives) !!}</div>

    <span class="field-label">Conteúdos Programáticos:</span>
    <div class="content-box">{!! \App\Shared\Infrastructure\Security\RichTextSanitizer::sanitize((string) $item->content_programmatic) !!}</div>

    <span class="field-label">Metodologia:</span>
    <div class="content-box">{!! \App\Shared\Infrastructure\Security\RichTextSanitizer::sanitize((string) $item->methodologies) !!}</div>

    <span class="field-label">Avaliação:</span>
    <div class="content-box">{!! \App\Shared\Infrastructure\Security\RichTextSanitizer::sanitize((string) $item->evaluations) !!}</div>

    <span class="field-label">Registros Complementares:</span>
    <div class="content-box">{!! \App\Shared\Infrastructure\Security\RichTextSanitizer::sanitize((string) ($item->complementary_records ?? '')) !!}</div>

    <span class="field-label">Parecer:</span>
    <div class="content-box">{!! \App\Shared\Infrastructure\Security\RichTextSanitizer::sanitize((string) $item->opinion) !!}</div>

   

    <table class="signature-table">
        <tr>
            <td><div class="sig-line">Assinatura do Docente</div></td>
            <td><div class="sig-line">Assinatura do Coordenador de Curso</div></td>
        </tr>
        <tr>
            <td class="pdf-signature-cell-spaced"><div class="sig-line">NAPNE / NAAf</div></td>
            <td class="pdf-signature-cell-spaced"><div class="sig-line">Setor Pedagógico / Assistência Estudantil</div></td>
        </tr>
    </table>

    {{-- Anexo II anexado ao final do documento individual --}}
    <div class="page-break"></div>
    
    <x-pdf.header
        title="Declaração"
        :meta="[
            'Estudante' => $pei->student->person->name,
            'Documento' => $pei->student->person->document ?? '___________',
        ]"
    />

    <div class="pdf-text-justify pdf-mt-40 pdf-line-height-2">
        <p>
            Declaro para os devidos fins que eu, <strong>{{ $pei->student->person->name }}</strong>, 
            CPF nº <strong>{{ $pei->student->person->document ?? '___________' }}</strong>, na condição de pessoa com deficiência 
            e tendo ingressado por reserva de vagas nesta instituição, estou ciente de que tenho direito ao apoio, 
            acompanhamentos e demais procedimentos previstos no processo de acessibilidade curricular - 
            Plano Educacional Individualizado (PEI).
        </p>

        <p class="pdf-mt-20">
            ( &nbsp; ) Desejo receber os acompanhamentos previstos.
        </p>
        <p>
            ( &nbsp; ) Declaro, outrossim, que me <strong>recuso</strong> a receber os acompanhamentos e demais procedimentos supramencionados.
        </p>

        <div class="pdf-mt-50 pdf-text-right">
            {{ config('app.city', 'Guanambi - BA') }}, {{ date('d') }} de {{ date('m') }} de {{ date('Y') }}.
        </div>

        <div class="pdf-mt-80 pdf-text-center">
            <div class="pdf-signature-line-black"></div>
            <p class="pdf-signature-caption">Assinatura do estudante ou responsável legal</p>
        </div>
    </div>

</body>
</html>
