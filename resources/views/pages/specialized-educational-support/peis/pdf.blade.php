<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>PEI - {{ $item->discipline->name }} - {{ $pei->student->person->name }}</title>
    <style>
        @page { margin: 1.5cm; }
        body { font-family: "Times New Roman", Times, serif; font-size: 12px; color: #000; line-height: 1.4; background: #fff; }
        
        .header { text-align: center; margin-bottom: 25px; text-transform: uppercase; font-weight: bold; }
        .header p { margin: 2px 0; font-size: 11px; }
        .header h1 { font-size: 14px; margin-top: 10px; border: 1px solid #000; padding: 8px; }

        .section-title { font-weight: bold; text-transform: uppercase; margin-top: 20px; margin-bottom: 5px; border-bottom: 2px solid #000; font-size: 11px; padding-bottom: 2px; }
        .field-label { font-weight: bold; display: block; margin-top: 12px; text-transform: uppercase; font-size: 10px; color: #333; }
        
        .content-box { border: 1px solid #000; padding: 10px; min-height: 40px; margin-top: 5px; width: 100%; box-sizing: border-box; text-align: justify; }
        
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .info-table td { border: 1px solid #000; padding: 8px; vertical-align: top; }
        
        .signature-table { width: 100%; margin-top: 40px; border-collapse: collapse; }
        .signature-table td { width: 50%; padding: 20px 10px 0 10px; text-align: center; }
        .sig-line { border-top: 1px solid #000; font-size: 9px; text-transform: uppercase; padding-top: 5px; }
        
        .page-break { page-break-after: always; }
        .clear { clear: both; }

        /* Renderização de HTML */
        .content-box p { margin: 0 0 10px 0; }
        .content-box ul, .content-box ol { margin-left: 20px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>PLANO EDUCACIONAL INDIVIDUALIZADO (PEI)</h1>
    </div>

    <table class="info-table">
        <tr>
            <td colspan="2"><span class="field-label">Nome do Estudante:</span> {{ $pei->student->person->name }}</td>
        </tr>
        <tr>
            <td width="60%"><span class="field-label">Curso:</span> {{ $pei->course->name }}</td>
            <td width="40%"><span class="field-label">Ano - Semestre/Trimestre:</span> {{ $pei->semester->label }}</td>
        </tr>
        <tr>
            <td width="60%"><span class="field-label">Componente Curricular:</span> {{ mb_strtoupper($item->discipline->name, 'UTF-8') }}</td>
            <td width="40%"><span class="field-label">Docente:</span> {{ $item->teacher->person->name }}</td>
        </tr>
    </table>

    <div class="section-title">Informações de Apoio Pedagógico (NAPNE)</div>
    
    <span class="field-label">Histórico (Trajetória do Estudante):</span>
    <div class="content-box">{{ $pei->studentContext->history }}</div>

    <span class="field-label">Necessidades Educacionais Específicas:</span>
    <div class="content-box">{{ $pei->studentContext->specific_educational_needs }}</div>

    <span class="field-label">Conhecimentos, Habilidades, Capacidades e Interesses:</span>
    <div class="content-box">{{ $pei->studentContext->strengths }}</div>

    <span class="field-label">Dificuldades Apresentadas:</span>
    <div class="content-box">{{ $pei->studentContext->difficulties }}</div>

    <div class="section-title">Adaptações Razoáveis e/ou Acessibilidades Curriculares</div>

    <span class="field-label">Objetivos Específicos:</span>
    <div class="content-box">{!! $item->specific_objectives !!}</div>

    <span class="field-label">Conteúdos Programáticos:</span>
    <div class="content-box">{!! $item->content_programmatic !!}</div>

    <span class="field-label">Metodologia:</span>
    <div class="content-box">{!! $item->methodologies !!}</div>

    <span class="field-label">Avaliação:</span>
    <div class="content-box">{!! $item->evaluations !!}</div>

    <table class="signature-table">
        <tr>
            <td><div class="sig-line">Assinatura do Docente</div></td>
            <td><div class="sig-line">Assinatura do Coordenador de Curso</div></td>
        </tr>
        <tr>
            <td style="padding-top: 40px;"><div class="sig-line">NAPNE / NAAf</div></td>
            <td style="padding-top: 40px;"><div class="sig-line">Setor Pedagógico / Assistência Estudantil</div></td>
        </tr>
    </table>

    {{-- Anexo II anexado ao final do documento individual --}}
    <div class="page-break"></div>
    
    <div class="header">
        <p>Instituto Federal de Educação, Ciência e Tecnologia do Rio Grande do Sul</p>
        <h1>ANEXO II - DECLARAÇÃO</h1>
    </div>

    <div style="text-align: justify; margin-top: 40px; line-height: 2;">
        <p>
            Declaro para os devidos fins que eu, <strong>{{ $pei->student->person->name }}</strong>, 
            CPF nº <strong>{{ $pei->student->person->document ?? '___________' }}</strong>, na condição de pessoa com deficiência 
            e tendo ingressado por reserva de vagas nesta instituição, estou ciente de que tenho direito ao apoio, 
            acompanhamentos e demais procedimentos previstos no processo de acessibilidade curricular - 
            Plano Educacional Individualizado (PEI).
        </p>

        <p style="margin-top: 20px;">
            ( &nbsp; ) Desejo receber os acompanhamentos previstos.
        </p>
        <p>
            ( &nbsp; ) Declaro, outrossim, que me <strong>recuso</strong> a receber os acompanhamentos e demais procedimentos supramencionados.
        </p>

        <div style="margin-top: 50px; text-align: right;">
            {{ config('app.city', 'Guanambi - BA') }}, {{ date('d') }} de {{ date('m') }} de {{ date('Y') }}.
        </div>

        <div style="margin-top: 80px; text-align: center;">
            <div style="border-top: 1px solid #000; width: 300px; margin: 0 auto;"></div>
            <p style="font-size: 10px; text-transform: uppercase;">Assinatura do estudante ou responsável legal</p>
        </div>
    </div>

</body>
</html>