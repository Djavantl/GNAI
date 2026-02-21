<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'GNAI - Sistema Escolar')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <!-- fontes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <!-- VITE (CSS + JS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    @include('partials.navbar')

    <x-messages.toast />

    <!-- Sidebar -->
    @include('partials.sidebar')

    <!-- Conteúdo Principal -->
    <main class="main-content">
        <div class="page-transition">
            @yield('content')
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>

        function initSelectSearch(element) {
            $(element).select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Selecione uma opção...',
                language: {
                    noResults: function() { return "Nenhum resultado encontrado"; }
                }
            });
        }
        $(document).ready(function() {
            $('.select-search').each(function() {
                initSelectSearch(this);
            });
            $('.select-search').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Selecione uma opção...', 
                allowClear: true, 
                language: {
                    noResults: function() {
                        return "Nenhum resultado encontrado";
                    }
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
