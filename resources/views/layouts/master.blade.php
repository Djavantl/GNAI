<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'GNAI - Sistema de Gestão de Acessibilidade')</title>
    <meta name="description" content="GNAI - Plataforma para o Atendimento Educacional Especializado (AEE).">
    <meta name="author" content="Equipe ADS-2026: Djavan Teixeira Lopes e Marley Teixeira Meira">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">

    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/webfonts/fa-solid-900.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/fonts/bootstrap-icons.woff2?2ab2cbb" as="font" type="font/woff2" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" media="print" onload="this.media='all'">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body>
@include('partials.navbar')
<x-messages.toast />
@include('partials.sidebar')

<main class="main-content">
    <div class="page-transition">
        @yield('content')
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>

<script src="https://vlibras.gov.br/app/vlibras-plugin.js" defer></script>
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js" defer></script>

<script>
    // Inicialização do VLibras após o carregamento do script deferido
    window.addEventListener('load', function() {
        if (window.VLibras) {
            new window.VLibras.Widget('https://vlibras.gov.br/app');
        }
    });

    // Inicialização do Select2 e CKEditor
    document.addEventListener('DOMContentLoaded', function() {
        const initSelect = () => {
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('.select-search').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: 'Selecione uma opção...',
                    allowClear: true,
                    language: { noResults: () => "Nenhum resultado encontrado" }
                });
            }
        };

        typeof $ !== 'undefined' && $.fn.select2 ? initSelect() : window.addEventListener('load', initSelect);

        // CKEditor
        const setupEditors = () => {
            if (typeof ClassicEditor !== 'undefined') {
                const allEditors = {};
                document.querySelectorAll('.rich-editor').forEach(element => {
                    ClassicEditor.create(element, {
                        toolbar: ['heading', '|', 'bold', 'italic', '|', 'link', '|', 'bulletedList', 'numberedList', '|', 'undo', 'redo']
                    })
                        .then(editor => { allEditors[element.name] = editor; })
                        .catch(err => console.error(err));
                });

                const form = document.querySelector('form');
                if (form) {
                    form.addEventListener('submit', () => {
                        Object.keys(allEditors).forEach(name => allEditors[name].updateSourceElement());
                    });
                }
            }
        };
        typeof ClassicEditor !== 'undefined' ? setupEditors() : window.addEventListener('load', setupEditors);
    });
</script>

@stack('scripts')

<div vw class="enabled">
    <div vw-access-button class="active"></div>
    <div vw-plugin-wrapper><div class="vw-plugin-top-wrapper"></div></div>
</div>
</body>
</html>
