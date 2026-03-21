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
        margin: -0.8rem 0 1rem 0 !important;
        font-size: 0.95rem;
        background-color: transparent;
    }

    .breadcrumb-item {
        display: flex;
        align-items: center;
        padding-left: 0 !important;
        font-weight: 600;
    }

    .breadcrumb-item::before {
        display: none !important;
        content: "" !important;
    }

    .breadcrumb-item:not(:last-child)::after {
        content: ">";
        margin-left: 0.6rem;
        margin-right: 0.6rem;
        color: #5A4FCF;
        font-weight: 400;
        display: inline-block;
        aria-hidden: true;
    }

    .breadcrumb-item a {
        text-decoration: none;
        color: #555e66;
        font-weight: inherit;
        transition: color 0.2s, text-decoration 0.2s;
    }

    .breadcrumb-item a:hover, .breadcrumb-item a:focus {
        text-decoration: underline;
        color: #4D44B5;
        outline: none;
    }

    .breadcrumb-item.active {
        color: #2D335B;
        font-weight: 700;
    }
</style>
