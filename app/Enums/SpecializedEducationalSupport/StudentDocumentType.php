<?php
namespace App\Enums\SpecializedEducationalSupport;

enum StudentDocumentType: string
{
    case HISTORY = 'history';
    case TECHNICAL_REPORT = 'technical report';
    case REPORT = 'report';
    case ASSESSMENT = 'assessment';
    case PLAN_AEE = 'plan_aee'; 
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::HISTORY => 'Histórico Escolar',
            self::TECHNICAL_REPORT => 'Laudo',
            self::REPORT => 'Relatório',
            self::ASSESSMENT => 'Avaliação',
            self::PLAN_AEE => 'Plano AEE',
            self::OTHER => 'Outro',
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public static function labels(): array
    {
        $map = [];
        foreach (self::cases() as $case) {
            $map[$case->value] = $case->label();
        }
        return $map;
    }
}
