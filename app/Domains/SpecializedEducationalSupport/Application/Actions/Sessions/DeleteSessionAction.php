<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Sessions;

use App\Domains\SpecializedEducationalSupport\Application\Services\Sessions\SessionNotificationSender;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Session;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeleteSessionAction
{
    public function __construct(
        private SessionNotificationSender $notifications,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(Session $session, int $userId, bool $sendNotification = false): void
    {
        $session->ensureCreatedBy($userId, 'excluí-la');

        $cancelledSession = DB::transaction(function () use ($session): ?Session {
            $lockedSession = Session::query()
                ->lockForUpdate()
                ->findOrFail($session->getKey());

            if ($lockedSession->isScheduled()) {
                $lockedSession->cancel('Agendamento cancelado automaticamente devido à exclusão do registro.');
                $lockedSession->save();
                $notificationSession = $lockedSession->fresh(['students.person', 'professional.person']);
            }

            $lockedSession->delete();

            return $notificationSession ?? null;
        });

        if ($sendNotification && $cancelledSession !== null) {
            $this->notifications->send(
                $cancelledSession,
                'Agendamento Cancelado',
                'Informamos que o seu agendamento foi cancelado. Motivo: Agendamento cancelado automaticamente devido à exclusão do registro.',
            );
        }
    }
}
