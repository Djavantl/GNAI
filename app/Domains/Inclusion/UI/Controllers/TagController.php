<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\UI\Controllers;

use App\Domains\Inclusion\Application\Actions\Tags\CreateTagAction;
use App\Domains\Inclusion\Application\Actions\Tags\DeleteTagAction;
use App\Domains\Inclusion\Application\Actions\Tags\UpdateTagAction;
use App\Domains\Inclusion\Application\Data\Tags\CreateTagData;
use App\Domains\Inclusion\Application\Data\Tags\ListTagsData;
use App\Domains\Inclusion\Application\Data\Tags\UpdateTagData;
use App\Domains\Inclusion\Application\Queries\Tags\ListTagsQuery;
use App\Domains\Inclusion\Application\Queries\Tags\ShowTagQuery;
use App\Domains\Inclusion\Domain\Exceptions\InvalidTag;
use App\Domains\Inclusion\Domain\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class TagController
{
    public function index(ListTagsData $filters, ListTagsQuery $query, Request $request): InertiaResponse
    {
        $tags = $query->execute($filters);

        return Inertia::render(
            'Inclusion/Tags/Index',
            [
                'tags' => $tags,
                'filters' => [
                    'name' => $filters->name ?? '',
                    'is_active' => $filters->isActive === null ? '' : ($filters->isActive ? '1' : '0'),
                ],
                'can' => [
                    'create' => $request->user()->can('tag.create'),
                    'show' => $request->user()->can('tag.show'),
                    'destroy' => $request->user()->can('tag.destroy'),
                ],
            ],
        );
    }

    public function create(Request $request): InertiaResponse
    {
        return Inertia::render('Inclusion/Tags/Create', [
            'can' => ['store' => $request->user()->can('tag.store')],
        ]);
    }

    /**
     * @throws InvalidTag
     */
    public function store(CreateTagData $data, CreateTagAction $action): RedirectResponse
    {
        $action->execute($data);

        return redirect()
            ->route('inclusive-radar.tags.index')
            ->with('success', 'Etiqueta cadastrada com sucesso!');
    }

    public function show(Tag $tag, ShowTagQuery $query, Request $request): InertiaResponse
    {
        $tag = $query->execute($tag);

        return Inertia::render(
            'Inclusion/Tags/Show',
            [
                'tag' => $tag,
                'can' => [
                    'edit' => $request->user()->can('tag.edit'),
                    'destroy' => $request->user()->can('tag.destroy'),
                ],
            ],
        );
    }

    public function edit(Tag $tag, Request $request): InertiaResponse
    {
        return Inertia::render(
            'Inclusion/Tags/Edit',
            [
                'tag' => $tag,
                'can' => ['update' => $request->user()->can('tag.update')],
            ],
        );
    }

    /**
     * @throws InvalidTag
     */
    public function update(UpdateTagData $data, Tag $tag, UpdateTagAction $action): RedirectResponse
    {
        $action->execute($tag, $data);

        return redirect()
            ->route('inclusive-radar.tags.index')
            ->with('success', 'Etiqueta atualizada com sucesso!');
    }

    /**
     * @throws Throwable
     */
    public function destroy(Tag $tag, DeleteTagAction $action): RedirectResponse
    {
        $action->execute($tag);

        return redirect()
            ->route('inclusive-radar.tags.index')
            ->with('success', 'Etiqueta removida com sucesso!');
    }
}
