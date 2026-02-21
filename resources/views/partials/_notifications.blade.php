{{-- 🔔 Partials: Notificações --}}
@auth
@php
    $unreadCount = Auth::user()->unreadNotifications()->count();
    $displayCount = $unreadCount > 99 ? '99+' : $unreadCount;
    $notifications = Auth::user()->unreadNotifications()->take(5)->get();
@endphp

<div class="dropdown me-3">
    <button class="btn btn-notif-circle position-relative"
            type="button"
            id="dropdownNotif"
            data-bs-toggle="dropdown"
            aria-expanded="false">
        
        <i class="fa fa-regular fa-bell" style="color: #4D44B5"></i>

        @if($unreadCount > 0)
            <span id="notif-count"
                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-2 border-white notif-badge-custom">
                {{ $displayCount }}
            </span>
        @endif
    </button>

    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3" style="width: 320px; border-radius: 15px; overflow: hidden;">
        <li class="px-3 py-3 d-flex justify-content-between align-items-center bg-light">
            <span class="fw-bold text-dark">Notificações</span>
            @if($unreadCount > 0)
                <span class="badge bg-primary rounded-pill">{{ $unreadCount }} novas</span>
            @endif
        </li>
        
        <div class="notification-scroll" style="max-height: 350px; overflow-y: auto;">
            @forelse($notifications as $notification)
                @php $data = $notification->data; @endphp
                <li>
                    <a href="{{ $data['url'] ?? '#' }}" 
                       class="dropdown-item py-3 border-bottom text-wrap notify-item" 
                       style="transition: background 0.2s;">
                        <div class="d-flex flex-column">
                            <span class="fw-semibold text-dark" style="font-size: 0.9rem;">
                                {{ $data['title'] ?? 'Nova atualização' }}
                            </span>
                            <small class="text-muted mb-1" style="font-size: 0.8rem;">
                                {{ $data['message'] ?? '' }}
                            </small>
                            <small class="text-primary fw-bold" style="font-size: 0.7rem;">
                                <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($data['created_at'] ?? now())->diffForHumans() }}
                            </small>
                        </div>
                    </a>
                </li>
            @empty
                <li class="py-5 text-center">
                    <i class="bi bi-bell-slash text-muted d-block mb-2" style="font-size: 1.5rem;"></i>
                    <span class="text-muted small">Você está em dia! Nenhuma notificação.</span>
                </li>
            @endforelse
        </div>

        <li>
            <a class="dropdown-item text-center small fw-bold py-3 text-primary bg-light" href="{{ route('notifications.index') }}">
                Ver todas as notificações
            </a>
        </li>
    </ul>
</div>
@endauth