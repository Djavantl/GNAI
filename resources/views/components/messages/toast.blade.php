@if(session('success') || session('error') || session('info') || $errors->any())
    <div id="toast-container">
        @php
            $type = 'info';
            $icon = 'fa-info-circle';
            $message = '';

            if (session('success')) {
                $type = 'success';
                $icon = 'fa-check-circle';
                $message = session('success');
            } elseif (session('error')) {
                $type = 'danger';
                $icon = 'fa-exclamation-circle';
                $message = session('error');
            } elseif ($errors->any()) {
                // Captura erros do Request Validation
                $type = 'danger';
                $icon = 'fa-exclamation-triangle';
                // Pega apenas o primeiro erro para não quebrar o layout do toast
                $message = $errors->first();
            } elseif (session('info')) {
                $message = session('info');
            }
        @endphp

        <div class="toast-custom {{ $type }}" id="toast-element">
            <div class="toast-content">
                <i class="fas {{ $icon }} fa-lg"></i>
                <div class="toast-body-text">
                    {{ $message }}
                </div>
                <button type="button" class="btn-close-toast" onclick="window.closeToast()">×</button>
            </div>
            <div class="toast-progress"></div>
        </div>
    </div>
@endif
