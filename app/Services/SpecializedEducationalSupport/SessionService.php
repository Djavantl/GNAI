<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Session;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SessionNotification;

class SessionService
{
    //listar all

    public function index()
    {
        return Session::get();
    }

    //email

    private function sendSessionEmails(Session $session, string $subject, string $text)
    {

        $session->load(['student.person', 'professional.person']);

        $emails = [
            $session->student->person->email,
            $session->professional->person->email
        ];

        foreach ($emails as $email) {
            if ($email) {
                Mail::to($email)->send(new SessionNotification($session, $subject, $text));
            }
        }
    }

    // criar

    public function create(array $data): Session
    {
        return DB::transaction(function () use ($data) {
            $session = Session::create($data);

            $this->sendSessionEmails($session, "Nova Sessão de Atendimento Agendada", "Uma nova sessão foi registrada para você.");

            return $session;
        });
    }

    // mostrar somente uma

    public function show(Session $session): Session
    {
        return $session->load(['student', 'professional']);
    }

    //atualizar

    public function update(Session $session, array $data): Session
    {
        return DB::transaction(function () use ($session, $data) {
            $session->update($data);

            $this->sendSessionEmails($session, "Sessão de Atendimento Atualizada", "Houve uma alteração nos detalhes da sua sessão.");

            return $session;
        });
    }

    //soft delete

    public function delete(Session $session): void
    {
        $this->sendSessionEmails($session, "Sessão de Atendimento CANCELADA", "Atenção: A sessão abaixo foi cancelada/removida.");
        
        $session->delete();
    }

    //restaurar

    public function restore(Session $session): Session
    {
        $session->restore();

        return $session;
    }

    //excluir definitivamente

    public function forceDelete(Session $session): void
    {
        $session->forceDelete();
    }
}
