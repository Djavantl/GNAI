<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Tests\Unit\Application\Data\Publications;

use App\Domains\Inclusion\Application\Data\Publications\CreatePublicationData;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

final class CreatePublicationDataTest extends TestCase
{
    public function test_it_maps_snake_case_input(): void
    {
        $data = CreatePublicationData::from([
            'title' => 'Semana Inclusiva',
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-01',
            'start_time' => '08:00',
            'end_time' => '10:00',
            'location' => 'Auditório',
            'has_schedule' => true,
            'is_active' => true,
            'tag_ids' => [3, 5],
            'speakers' => [['id' => 7, 'participation' => 'Palestrante'], ['name' => 'Convidada']],
            'cover_alt' => 'Auditório lotado',
        ]);

        self::assertSame('2026-08-01', $data->startDate);
        self::assertSame('10:00', $data->endTime);
        self::assertTrue($data->hasSchedule);
        self::assertTrue($data->isActive);
        self::assertSame([3, 5], $data->tagIds);
        self::assertSame([['id' => 7, 'participation' => 'Palestrante'], ['name' => 'Convidada']], $data->speakers);
        self::assertSame('Auditório lotado', $data->coverAlt);
    }

    public function test_it_applies_checkbox_defaults(): void
    {
        $data = CreatePublicationData::from(['title' => 'Semana Inclusiva']);

        self::assertTrue($data->hasSchedule);
        self::assertFalse($data->isActive);
        self::assertFalse($data->isFeatured);
        self::assertSame('event', $data->type);
        self::assertSame([], $data->tagIds);
        self::assertSame([], $data->speakers);
    }

    public function test_it_requires_the_period_only_for_scheduled_publications(): void
    {
        $withoutSchedule = CreatePublicationData::validateAndCreate([
            'title' => 'Comunicado',
            'has_schedule' => '0',
        ]);

        self::assertFalse($withoutSchedule->hasSchedule);

        try {
            CreatePublicationData::validate(['title' => 'Evento', 'has_schedule' => '1']);
            self::fail('Publicações com período devem exigir datas, horários e local.');
        } catch (ValidationException $exception) {
            self::assertEqualsCanonicalizing(
                ['start_date', 'end_date', 'start_time', 'end_time', 'location'],
                array_keys($exception->errors()),
            );
        }
    }
}
