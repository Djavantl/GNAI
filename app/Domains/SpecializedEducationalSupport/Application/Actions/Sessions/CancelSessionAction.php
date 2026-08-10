<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Sessions;

use App\Domains\SpecializedEducationalSupport\Application\Data\Sessions\CancelSessionData;
use App\Domains\SpecializedEducationalSupport\Application\Services\Sessions\SessionNotificationSender;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Session;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CancelSessionAction
{
    public function __construct(
        private SessionNotificationSender $notifications,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(Session $session, CancelSessionData $data, int $userId): Session
    {
        $session->ensureCreatedBy($userId, 'cancelá-la');
        $session->ensureCanBeCancelled();

        $cancelledSession = DB::transaction(function () use ($session, $data): Session {
            $lockedSession = Session::query()
                ->lockForUpdate()
                ->findOrFail($session->getKey());

            $lockedSession->ensureCanBeCancelled();
            $lockedSession->cancel($data->cancellationReason);
            $lockedSession->save();

            return $lockedSession->fresh(['students.person', 'professional.person', 'aeeRecord', 'pedagogicalRecord']);
        });

        if ($data->sendNotification) {
            $this->notifications->send(
                $cancelledSession,
                'Agendamento Cancelado',
                "Informamos que o seu agendamento foi cancelado. Motivo: {$data->cancellationReason}",
            );
        }

        return $cancelledSession;
    }
}
