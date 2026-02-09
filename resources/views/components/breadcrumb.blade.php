@props(['items'])

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        @foreach($items as $label => $url)
            <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}">
                @if(!$loop->last)
                    <a href="{{ $url }}">{{ $label }}</a>
                @else
                    <span aria-current="page">{{ $label }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>

<style>
    .breadcrumb {
        display: flex;
        flex-wrap: wrap;
        list-style: none;
        padding: 0;
        /* O margin-top negativo "puxa" o componente para cima, compensando o padding do main-content */
        margin: -0.8rem 0 1rem 0 !important;
        font-size: 0.95rem;
        background-color: transparent;
    }

    .breadcrumb-item {
        display: flex;
        align-items: center;
        padding-left: 0 !important;
        /* Define a espessura da fonte para todos os itens */
        font-weight: 600;
    }

    /* Remove definitivamente a barra "/" do Bootstrap */
    .breadcrumb-item::before {
        display: none !important;
        content: "" !important;
        padding: 0 !important;
    }

    /* Estilização do separador ">" */
    .breadcrumb-item:not(:last-child)::after {
        content: ">";
        margin-left: 0.6rem;
        margin-right: 0.6rem;
        /* Cor roxa para combinar com sua identidade visual */
        color: var(--primary-color, #4D44B5);
        font-weight: 400; /* O separador não precisa ser grosso */
        display: inline-block;
    }

    .breadcrumb-item a {
        text-decoration: none;
        /* Cor cinza para links (não ativos) para dar contraste */
        color: #6c757d;
        font-weight: inherit;
        transition: color 0.2s;
    }

    .breadcrumb-item a:hover {
        text-decoration: underline;
        color: var(--primary-color, #4D44B5);
    }

    /* Item atual (último) */
    .breadcrumb-item.active {
        /* Roxo escuro e mais grosso para destacar a página atual */
        color: #303972;
        font-weight: 700;
    }
</style>
