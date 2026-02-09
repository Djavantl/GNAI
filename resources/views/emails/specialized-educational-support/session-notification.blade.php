<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2 style="color: #2d3748;">{{ $title }}</h2>
    <p>Olá,</p>
    <p>{{ $messageContent }}</p>

    <div style="background: #f7fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
        <p><strong>Detalhes da Sessão:</strong></p>
        <ul style="list-style: none; padding: 0;">
            <li>📅 <strong>Data:</strong> {{ \Carbon\Carbon::parse($session->session_date)->format('d/m/Y') }}</li>
            <li>⏰ <strong>Horário:</strong> {{ $session->start_time }} até {{ $session->end_time ?? 'Não definido' }}</li>
            <li>📍 <strong>Local:</strong> {{ $session->location }}</li>
            <li>🏷️ <strong>Tipo:</strong> {{ $session->type }}</li>
        </ul>
    </div>

    <p style="margin-top: 20px;"><strong>Objetivo:</strong><br> {{ $session->session_objective }}</p>

    <hr style="border: 0; border-top: 1px solid #edf2f7; margin: 20px 0;">
    <p style="font-size: 12px; color: #718096;">Este é um e-mail automático enviado pelo Sistema NAPNE.</p>
</body>
</html>