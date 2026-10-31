<?php

declare(strict_types=1);

namespace App\Domains\Settings\Domain\Exceptions;

use App\Shared\Domain\Exceptions\BusinessRuleException;
use Illuminate\Support\Str;

final class MailTestFailed extends BusinessRuleException
{
    /**
     * Mensagem curta para a interface; o detalhe técnico completo fica no log.
     */
    public static function fromDetail(string $detail): self
    {
        $normalized = mb_strtolower($detail);
        $matches = static fn (array $needles): bool => Str::contains($normalized, $needles);

        return new self('E-mail de teste não enviado: '.match (true) {
            $matches(['authenticat', '"535', ' 535', 'username and password']) => 'o servidor recusou o usuário ou a senha. Confira o usuário e, no Gmail ou Microsoft, gere uma nova senha de app.',
            $matches(['getaddrinfo', 'name or service not known', 'connection refused', 'timed out', 'network is unreachable', 'no route to host']) => 'não foi possível conectar ao servidor SMTP. Confira o servidor e a porta.',
            $matches(['ssl operation failed', 'starttls', 'certificate', 'crypto', 'wrong version number', 'tls']) => 'falha na conexão segura. Confira a segurança e a porta (587 com STARTTLS ou 465 com SSL/TLS).',
            $matches(['connection could not be established']) => 'não foi possível conectar ao servidor SMTP. Confira o servidor, a porta e a segurança.',
            $matches(['"550', '"553', '"554', ' 550', ' 553', ' 554', 'sender', 'relay']) => 'o servidor recusou o remetente ou o destinatário. Confira o e-mail de envio e o destino.',
            default => 'confira as configurações de e-mail. Detalhe: '.Str::limit($detail, 120),
        });
    }
}
