<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>PEI Completo - {{ $pei->student->person->name }}</title>
    <style>
        @page { margin: 1.5cm; }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12px;
            color: #000;
            line-height: 1.4;
            background: #fff;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .header p {
            margin: 2px 0;
            font-size: 11px;
        }

        .header h1 {
            font-size: 14px;
            margin-top: 10px;
            border: 1px solid #000;
            padding: 8px 16px;
        }

        .section-title {
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 20px;
            margin-bottom: 5px;
            border-bottom: 2px solid #000;
            font-size: 11px;
            padding-bottom: 2px;
        }

        .field-label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
            text-transform: uppercase;
            font-size: 10px;
            color: #333;
        }

        .content-box {
            border: 1px solid #000;
            padding: 8px 16px 8px 10px;
            min-height: 40px;
            margin-top: 5px;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
            text-align: justify;
            overflow: hidden;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            table-layout: fixed;
        }

        .info-table td {
            border: 1px solid #000;
            padding: 8px 16px 8px 10px;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
            box-sizing: border-box;
            overflow: hidden;
        }

        .disc-card {
            border: 1px solid #000;
            padding: 14px 18px 16px 14px;
            margin-top: 12px;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
            page-break-inside: avoid;
            overflow: hidden;
        }

        /* Tudo dentro do disc-card respeita os limites */
        .disc-card * {
            max-width: 100%;
            box-sizing: border-box;
        }

        .disc-card .info-table {
            width: 100%;
            table-layout: fixed;
        }

        .disc-card .info-table td {
            overflow: hidden;
            word-wrap: break-word;
            overflow-wrap: break-word;
            padding: 8px 16px 8px 10px;
        }

        .disc-card .content-box {
            width: 100%;
            max-width: 100%;
            overflow: hidden;
            padding: 8px 16px 8px 10px;
        }

        .disc-head {
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 8px;
            font-size: 11px;
            line-height: 1.3;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .signature-table {
            width: 100%;
            margin-top: 40px;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .signature-table td {
            width: 50%;
            padding: 20px 10px 0 10px;
            text-align: center;
            vertical-align: top;
        }

        .sig-line {
            border-top: 1px solid #000;
            font-size: 9px;
            text-transform: uppercase;
            padding-top: 5px;
        }

        .page-break {
            page-break-after: always;
        }

        .content-box p {
            margin: 0 0 8px 0;
            max-width: 100%;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .content-box ul,
        .content-box ol {
            margin: 0 0 8px 18px;
            padding-left: 18px;
            max-width: 100%;
        }

        .content-box li {
            margin-bottom: 4px;
            max-width: 100%;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .content-box h1,
        .content-box h2,
        .content-box h3,
        .content-box h4,
        .content-box h5,
        .content-box h6 {
            margin: 0 0 8px 0;
            max-width: 100%;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .content-box table {
            width: 100% !important;
            max-width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .content-box table td,
        .content-box table th {
            border: 1px solid #000;
            padding: 4px 10px 4px 6px;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .content-box img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        .content-box * {
            max-width: 100%;
            box-sizing: border-box;
        }

        .muted {
            font-size: 10px;
            font-style: italic;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>PLANO EDUCACIONAL INDIVIDUALIZADO (PEI) - COMPLETO</h1>
    </div>

    <table class="info-table">
        <tr>
            <td colspan="2">
                <span class="field-label">Nome do Estudante:</span>
                {{ $pei->student->person->name }}
            </td>
        </tr>
        <tr>
            <td width="60%">
                <span class="field-label">Curso:</span>
                {{ $pei->course->name }}
            </td>
            <td width="40%">
                <span class="field-label">Ano - Semestre:</span>
                {{ $pei->semester->label }}
            </td>
        </tr>
        <tr>
            <td width="60%">
                <span class="field-label">PEI Atual:</span>
                {{ $pei->is_current ? 'Sim' : 'Não' }}
            </td>
            <td width="40%">
                <span class="field-label">Versão:</span>
                {{ $pei->version }}
            </td>
        </tr>
    </table>

    <div class="section-title">Informações de Apoio Pedagógico (NAPNE)</div>

    <span class="field-label">Histórico (Trajetória do Estudante):</span>
    <div class="content-box">
        {!! $pei->studentContext->history ?? '' !!}
    </div>

    <span class="field-label">Necessidades Educacionais Específicas:</span>
    <div class="content-box">
        {!! $pei->studentContext->specific_educational_needs ?? '' !!}
    </div>

    <span class="field-label">Conhecimentos e Interesses:</span>
    <div class="content-box">
        {!! $pei->studentContext->knowledge ?? '' !!}
    </div>

    <span class="field-label">Dificuldades Apresentadas:</span>
    <div class="content-box">
        {!! $pei->studentContext->difficulties ?? '' !!}
    </div>

    <div class="section-title">Adaptações Razoáveis e/ou Acessibilidades Curriculares</div>

    @forelse ($peiDisciplines as $index => $item)
        @if($index > 0)
            <div class="page-break"></div>
        @endif

        
            <div class="disc-head">
                Disciplina {{ $index + 1 }} de {{ $peiDisciplines->count() }}:
                {{ mb_strtoupper($item->discipline->name, 'UTF-8') }}
            </div>

            <table class="info-table" style="margin-bottom: 10px; table-layout: fixed; width: 100%;">
                <colgroup>
                    <col style="width: 60%;">
                    <col style="width: 40%;">
                </colgroup>
                <tr>
                    <td>
                        <span class="field-label">Docente:</span>
                        {{ $item->teacher->person->name ?? 'Não informado' }}
                    </td>
                    <td>
                        <span class="field-label">Criado por:</span>
                        {{ $item->creator->name ?? 'Sistema' }}
                    </td>
                </tr>
            </table>

            <span class="field-label">Objetivos Específicos:</span>
            <div class="content-box">
                {!! $item->specific_objectives ?? '' !!}
            </div>

            <span class="field-label">Conteúdos Programáticos:</span>
            <div class="content-box">
                {!! $item->content_programmatic ?? '' !!}
            </div>

            <span class="field-label">Metodologia:</span>
            <div class="content-box">
                {!! $item->methodologies ?? '' !!}
            </div>

            <span class="field-label">Avaliação:</span>
            <div class="content-box">
                {!! $item->evaluations ?? '' !!}
            </div>

            <span class="field-label">Registros Complementares:</span>
            <div class="content-box">
                {!! $item->complementary_records ?? '' !!}
            </div>

            <span class="field-label">Parecer:</span>
            <div class="content-box">
                {!! $item->opinion ?? '' !!}
            </div>

        
    @empty
        <div class="content-box">
            Nenhuma disciplina vinculada a este PEI.
        </div>
    @endforelse

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

    <div class="page-break"></div>

    <div class="header">
        <h1>DECLARAÇÃO</h1>
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
            <p style="font-size: 10px; text-transform: uppercase;">
                Assinatura do estudante ou responsável legal
            </p>
        </div>
    </div>

</body>
</html>
