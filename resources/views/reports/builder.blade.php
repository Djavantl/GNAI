@extends('layouts.master')

@section('title', 'Gerador de Relatórios')

@section('content')

    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Relatórios' => null,
        ]" />
    </div>

    <div class="report-builder d-flex justify-content-between align-items-start mb-4">
        <div class="report-builder__intro">
            <h2 class="text-title">Gerador de Relatórios e Consultas</h2>
            <p class="text-muted mb-0">
                Monte seu relatório de forma simples, escolhendo os dados, os campos e os filtros que deseja visualizar.
            </p>
        </div>
    </div>

    <div class="report-builder custom-table-card shadow-sm border rounded-3 overflow-hidden bg-white">
        <div class="row g-0">

            <x-forms.section title="1. Escolha o assunto do relatório" />
            <div class="col-12 p-4 border-bottom">
                <div class="report-guidance mb-0">
                    <i class="report-guidance__icon fas fa-circle-info" aria-hidden="true"></i>
                    <div>
                        <strong class="report-guidance__title">Comece por aqui</strong>
                        <span class="report-guidance__text">
                            Primeiro, escolha sobre o que será o relatório. Depois, mostraremos as informações que você poderá incluir.
                        </span>
                    </div>
                </div>

                <div class="row g-3 align-items-end mt-3">
                    <div class="col-12 col-lg-7">
                        <x-forms.select
                            name="model-select"
                            label="Assunto do relatório"
                            id="model-select"
                            :options="[]"
                            required
                        />
                        <div id="model-select-error" class="report-field-error" role="alert"></div>
                    </div>
                </div>
            </div>

            <x-forms.section title="2. Escolha as informações do relatório" />
            <div class="col-12 p-4 border-bottom">
                <div class="report-guidance mb-4">
                    <i class="report-guidance__icon fas fa-lightbulb" aria-hidden="true"></i>
                    <div>
                        <strong class="report-guidance__title">Escolha o que deseja visualizar</strong>
                        <span class="report-guidance__text">
                            Marque as informações que deseja incluir. Escolher somente o que for necessário deixa o relatório mais fácil de entender.
                        </span>
                    </div>
                </div>

                <div class="mb-4">
                    <p class="report-section-label">Informações principais</p>
                    <div id="columns-container" class="d-flex flex-wrap gap-2">
                        <span class="text-muted small fst-italic">
                            Escolha primeiro o assunto do relatório para ver as informações disponíveis.
                        </span>
                    </div>
                    <div id="columns-container-error" class="report-field-error" role="alert"></div>
                </div>

                <div class="border-top my-4"></div>

                <div id="relations-section" class="d-none">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <p class="report-section-label mb-0">Informações complementares</p>
                        <span class="report-optional">(opcional)</span>
                    </div>

                    <div class="report-guidance mb-4">
                        <i class="report-guidance__icon fas fa-circle-plus" aria-hidden="true"></i>
                        <div>
                            <strong class="report-guidance__title">Deseja acrescentar outras informações?</strong>
                            <span class="report-guidance__text">
                                Você também pode incluir informações associadas ao assunto escolhido, como responsáveis, documentos ou registros relacionados.
                            </span>
                        </div>
                    </div>

                    <div class="row g-3 align-items-end mb-3" style="max-width: 760px;">
                        <div class="col-12 col-md-8">
                            <x-forms.select
                                name="relation-select"
                                label="Adicionar informação complementar"
                                id="relation-select"
                                :options="[]"
                            />
                            <div id="relation-select-error" class="report-field-error" role="alert"></div>
                        </div>

                        <div class="col-12 col-md-4 ">
                            <div class="w-100">
                                @can('report.builder')
                                    <x-buttons.submit-button
                                        type="button"
                                        id="btn-add-relation"
                                        variant="new"
                                        class=" mb-4"
                                    >
                                        <i class="fas fa-plus"></i> Adicionar
                                    </x-buttons.submit-button>
                                @endcan
                            </div>
                        </div>
                    </div>

                    <div id="added-relations-area"></div>
                </div>
            </div>

            <x-forms.section title="3. Mostre apenas o que você precisa" />
            <div class="col-12 p-4 border-bottom">
                <div class="report-guidance mb-4">
                    <i class="report-guidance__icon fas fa-filter" aria-hidden="true"></i>
                    <div>
                        <strong class="report-guidance__title">Deseja limitar os resultados?</strong>
                        <span class="report-guidance__text">
                            Esta etapa é opcional. Use as opções abaixo para encontrar informações específicas, como um nome, uma data ou uma situação.
                        </span>
                    </div>
                </div>

                <div id="filters-list" class="d-flex flex-column gap-3"></div>

                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" id="filter-related-values">
                    <label class="form-check-label small" for="filter-related-values">
                        Nas informações complementares, mostrar somente os itens que correspondem à busca
                    </label>
                </div>

                <div class="mt-3">
                    <x-buttons.submit-button type="button" variant="secondary" onclick="addFilterRow()">
                        <i class="fas fa-plus"></i> Adicionar critério de busca
                    </x-buttons.submit-button>
                </div>
            </div>

            <div class="col-12 p-4 border-bottom bg-light">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <p class="mb-0 fw-semibold">Pronto para visualizar?</p>
                        <small class="text-muted">
                            Gere a prévia antes de exportar para conferir se o resultado está correto.
                        </small>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        @can('report.run')
                            <x-buttons.submit-button type="button" id="btn-run" variant="new" onclick="runReport()" disabled>
                                <i class="fas fa-table"></i> Gerar prévia
                            </x-buttons.submit-button>
                        @endcan
                        @can('report.pdf')
                            <x-buttons.submit-button type="button" variant="secondary" onclick="exportPdf()">
                                <i class="fas fa-file-pdf"></i> Exportar PDF
                            </x-buttons.submit-button>
                        @endcan
                    </div>
                </div>
            </div>

            <x-forms.section title="4. Prévia do relatório" />
            <div class="col-12 p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <p class="fw-semibold mb-1">Resultado gerado</p>
                        <small class="text-muted">
                            Confira o conteúdo antes de seguir com a exportação.
                        </small>
                    </div>
                    <span id="preview-count-badge" class="badge bg-purple-light text-purple-dark border d-none"></span>
                </div>

                <div id="preview-empty" class="text-center py-5 px-4 border rounded-3 bg-light">
                    <i class="fas fa-chart-bar text-muted mb-3 d-block" style="font-size:2.5rem;opacity:.2"></i>
                    <p class="text-muted mb-0">Nenhum relatório gerado ainda.</p>
                    <small class="text-muted">Escolha as opções acima e clique em “Gerar prévia”.</small>
                </div>

                <div id="preview-loading" class="d-none text-center py-5">
                    <div class="spinner-border text-secondary mb-3" role="status"></div>
                    <p class="text-muted small mb-0">Buscando dados...</p>
                </div>

                <div id="preview-error" class="d-none">
                    <div class="alert alert-danger mb-0">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <span id="preview-error-msg"></span>
                    </div>
                </div>

                <div id="preview-section" class="d-none border rounded-3 overflow-hidden">
                    <div class="table-responsive">
                        <table class="report-preview-table table table-sm table-hover mb-0">
                            <thead class="table-light" id="preview-head"></thead>
                            <tbody id="preview-body"></tbody>
                        </table>
                    </div>

                    <div class="px-4 py-2 border-top bg-light d-flex justify-content-between align-items-center">
                        <small class="text-muted" id="preview-footer-info"></small>
                        <x-buttons.submit-button type="button" variant="secondary" onclick="copyTable()">
                            <i class="fas fa-copy"></i> Copiar
                        </x-buttons.submit-button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            const MAX_COLUMNS = 8;

            document.addEventListener('change', function (e) {

                if (e.target.name !== 'cols') return;

                const selected = document.querySelectorAll(
                    'input[name="cols"]:checked'
                );

                if (selected.length > MAX_COLUMNS) {
                    e.target.checked = false;
                    setFieldError('columns-container', `Você pode selecionar no máximo ${MAX_COLUMNS} informações.`)
                    return
                }

                clearFieldError('columns-container')

            });

            const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? ''

            let meta = null
            let addedRelations = []

            document.addEventListener('DOMContentLoaded', loadEntities)

            document.getElementById('btn-add-relation').addEventListener('click', () => {
                const sel = document.getElementById('relation-select')
                const relName = sel.value

                if (!relName) return setFieldError('relation-select', 'Escolha uma informação complementar.')

                clearFieldError('relation-select')

                const rel = (meta.relations ?? []).find(r => r.name === relName)
                if (!rel) return

                if (addedRelations.find(r => r.name === relName)) {
                    return setFieldError('relation-select', 'Essa informação já foi adicionada.')
                }

                addedRelations.push(rel)
                populateRelationSelect()
                renderAddedRelations()
                updateFilterOptions()
            })

            document.getElementById('relation-select').addEventListener('change', () => {
                clearFieldError('relation-select')
            })

            async function loadEntities () {
                try {
                    const list = await fetch(@json(route('reports.sources')), { credentials: 'same-origin' }).then(r => r.json())
                    const sel = document.getElementById('model-select')

                    sel.innerHTML = '<option value="">Selecione uma opção</option>'
                    list.forEach(e => {
                        let label = e.label
                        if (typeof label === 'string' && label.startsWith('database.models.')) {
                            label = e.key
                        }
                        sel.innerHTML += `<option value="${e.key}">${label}</option>`
                    })

                    sel.addEventListener('change', () => {
                        clearFieldError('model-select')
                        resetAll()
                        if (sel.value) loadMeta(sel.value)
                    })
                } catch {
                    document.getElementById('model-select').innerHTML = '<option value="">Não foi possível carregar</option>'
                    setFieldError('model-select', 'Não foi possível carregar os assuntos do relatório. Tente novamente.')
                }
            }

            async function loadMeta (modelClass) {
                try {
                    const res = await fetch(`{{ route('reports.metadata') }}?source=${encodeURIComponent(modelClass)}`, {
                        credentials: 'same-origin'
                    })

                    if (!res.ok) throw new Error('HTTP ' + res.status)

                    meta = await res.json()
                    meta.key = modelClass

                    renderColumns()
                    populateRelationSelect()
                    document.getElementById('relations-section').classList.toggle(
                        'd-none',
                        !(meta.relations ?? []).length
                    )
                    document.getElementById('btn-run').removeAttribute('disabled')
                    updateFilterOptions()
                } catch {
                    setFieldError('model-select', 'Não foi possível carregar as informações deste assunto. Tente novamente.')
                }
            }

            function renderColumns () {
                const c = document.getElementById('columns-container')
                c.innerHTML = ''

                Object.entries(meta.columns ?? {}).forEach(([k, label]) => {
                    c.innerHTML += `
                        <label class="report-option">
                            <input type="checkbox" name="cols" value="${k}" class="form-check-input mt-0">
                            <span>${label}</span>
                        </label>`
                })
            }

            function populateRelationSelect () {
                const sel = document.getElementById('relation-select')
                sel.innerHTML = '<option value="">Selecione uma opção</option>'

                ;(meta.relations ?? [])
                    .filter(rel => !addedRelations.some(added => added.name === rel.name))
                    .forEach(rel => {
                    sel.innerHTML += `<option value="${rel.name}">${rel.label}</option>`
                    })
            }

            function renderAddedRelations () {
                const area = document.getElementById('added-relations-area')
                const selectedColumns = new Set(
                    Array.from(area.querySelectorAll('input[name="cols"]:checked'))
                        .map(input => input.value)
                )

                area.innerHTML = ''

                addedRelations.forEach(rel => {
                    const colsHtml = Object.entries(rel.columns ?? {}).map(([ck, cLabel]) => `
                        <label class="report-option">
                            <input type="checkbox" name="cols" value="${rel.name}.${ck}" class="form-check-input mt-0">
                            <span>${cLabel}</span>
                        </label>`).join('')

                    const pivotHtml = rel.pivot?.columns
                        ? `<div class="mt-3 pt-3 border-top">
                               <small class="text-muted d-block mb-2">Informações adicionais</small>
                               <div class="d-flex flex-wrap gap-2">
                                   ${Object.entries(rel.pivot.columns).map(([pk, pl]) => `
                                       <label class="report-option">
                                           <input type="checkbox" name="cols" value="${rel.name}.pivot.${pk}" class="form-check-input mt-0">
                                           <span>${pl}</span>
                                       </label>`).join('')}
                               </div>
                           </div>`
                        : ''
                    area.innerHTML += `
                        <div class="report-relation-card">
                            <div class="report-relation-card__header">
                                <strong>${rel.label}</strong>
                                <x-buttons.submit-button
                                    type="button"
                                    variant="danger"
                                    class="btn btn-sm py-1 px-4"
                                    onclick="removeRelation('${rel.name}')"
                                >
                                    <i class="fas fa-times"></i>
                                </x-buttons.submit-button>
                            </div>
                            <div class="p-3">
                                <div class="d-flex flex-wrap gap-2">${colsHtml}</div>
                                ${pivotHtml}
                            </div>
                        </div>`
                })

                area.querySelectorAll('input[name="cols"]').forEach(input => {
                    input.checked = selectedColumns.has(input.value)
                })
            }

            function removeRelation (name) {
                addedRelations = addedRelations.filter(r => r.name !== name)
                populateRelationSelect()
                renderAddedRelations()
                updateFilterOptions()
            }

            function updateFilterOptions () {
                const opts = []

                ;(meta?.filters ?? []).forEach(filter => {
                    opts.push({
                        value: filter.key,
                        label: filter.label,
                        type: filter.type,
                        operators: filter.operators,
                        options: filter.options ?? {},
                    })
                })

                addedRelations.forEach(rel => {
                    ;(rel.filter_definitions ?? []).forEach(definition => {
                        opts.push({
                            value: `${rel.name}.${definition.key}`,
                            label: `${rel.label} › ${definition.label}`,
                            type: definition.type ?? 'text',
                            operators: definition.operators ?? operatorsForType(definition.type ?? 'text'),
                            options: definition.options ?? {},
                        })
                    })

                })

                document.querySelectorAll('.f-col').forEach(sel => {
                    const cur = sel.value
                    sel.innerHTML = opts.map(o => `<option value="${o.value}">${o.label}</option>`).join('')
                    if (cur) sel.value = cur
                })

                window.__filterOpts = opts
            }

            function operatorsForType (type) {
                if (type === 'boolean') return ['=']
                if (type === 'date') return ['=', '>=', '<=']
                if (type === 'select') return ['=', '!=']

                return ['like', '=']
            }

            function addFilterRow () {
                if (!meta) return setFieldError('model-select', 'Escolha primeiro o assunto do relatório.')

                const opts = window.__filterOpts ?? []

                const div = document.createElement('div')
                div.className = 'border rounded-3 p-3 bg-light'
                div.innerHTML = `
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-lg-5">
                            <label class="form-label small text-muted mb-1">Informação</label>
                            <select class="form-select form-select-sm f-col">
                                ${opts.map(o => `<option value="${o.value}">${o.label}</option>`).join('')}
                            </select>
                        </div>

                        <div class="col-6 col-lg-3">
                            <label class="form-label small text-muted mb-1">Como procurar</label>
                            <select class="form-select form-select-sm f-op"></select>
                        </div>

                        <div class="col-6 col-lg-3">
                            <label class="form-label small text-muted mb-1">O que procurar</label>
                            <div class="f-value-container"></div>
                        </div>

                        <div class="col-12 col-lg-1 d-grid">
                            <x-buttons.submit-button
                                type="button"
                                variant="danger"
                                class="w-100"
                                onclick="this.closest('.border').remove()"
                            >
                                <i class="fas fa-trash-alt"></i>
                            </x-buttons.submit-button>
                        </div>
                    </div>`
                document.getElementById('filters-list').appendChild(div)

                const fieldSelect = div.querySelector('.f-col')
                fieldSelect.addEventListener('change', () => configureFilterRow(div))
                configureFilterRow(div)
            }

            function configureFilterRow (row) {
                const field = row.querySelector('.f-col').value
                const definition = (window.__filterOpts ?? []).find(option => option.value === field)
                if (!definition) return

                const operatorLabels = {
                    eq: 'é igual a',
                    neq: 'diferente de',
                    contains: 'contém',
                    gt: 'maior que',
                    gte: 'a partir de',
                    lt: 'menor que',
                    lte: 'até',
                }

                row.querySelector('.f-op').innerHTML = definition.operators
                    .map(operator => `<option value="${operator}">${operatorLabels[operator] ?? operator}</option>`)
                    .join('')

                const container = row.querySelector('.f-value-container')
                const options = Object.entries(definition.options ?? {})

                if (options.length) {
                    container.innerHTML = `<select class="form-select form-select-sm f-val">
                        ${options.map(([value, label]) => `<option value="${esc(value)}">${esc(label)}</option>`).join('')}
                    </select>`
                    return
                }

                const inputType = definition.type === 'date' ? 'date' : 'text'
                container.innerHTML = `<input type="${inputType}" class="form-control form-control-sm f-val" placeholder="Digite o que deseja encontrar...">`
            }

            async function runReport () {
                const cols = Array.from(document.querySelectorAll('input[name="cols"]:checked')).map(i => i.value)

                if (!meta) return setFieldError('model-select', 'Escolha o assunto do relatório.')
                if (!cols.length) return setFieldError('columns-container', 'Marque pelo menos uma informação para gerar a prévia.')

                clearFieldError('columns-container')

                const filters = Array.from(document.querySelectorAll('#filters-list .border')).map(row => ({
                    field: row.querySelector('.f-col').value,
                    operator: row.querySelector('.f-op').value,
                    value: row.querySelector('.f-val').value,
                })).filter(f => f.value !== '')

                showLoading()

                try {
                    const res = await fetch(@json(route('reports.run')), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: JSON.stringify({
                            source: meta.key,
                            columns: cols,
                            filters,
                            limit: 200,
                            filter_related_values: document.getElementById('filter-related-values').checked,
                        }),
                    })

                    const data = await res.json()
                    if (!res.ok || data.error) throw new Error(data.error ?? data.message ?? 'Erro desconhecido')

                    const rows = data.rows ?? []
                    if (!rows.length) return showEmpty('Nenhum resultado encontrado para os filtros aplicados.')

                    const headers = Object.values(data.headers ?? {})
                    if (!headers.length) {
                        headers.push(...Object.keys(rows[0]).map(k => getLabelForKey(k)))
                    }
                    renderPreview(rows, headers)
                } catch (err) {
                    showError(err.message)
                }
            }

            function renderPreview (rows, headers) {
                hideStates()
                document.getElementById('preview-section').classList.remove('d-none')

                document.getElementById('preview-head').innerHTML =
                    '<tr>' + headers.map(h => `<th class="text-nowrap">${esc(h)}</th>`).join('') + '</tr>'

                document.getElementById('preview-body').innerHTML = rows.map(r =>
                    '<tr>' + Object.values(r).map(v => `<td>${esc(v ?? '—')}</td>`).join('') + '</tr>'
                ).join('')

                const badge = document.getElementById('preview-count-badge')
                badge.textContent = `${rows.length} registro${rows.length !== 1 ? 's' : ''}`
                badge.classList.remove('d-none')

                document.getElementById('preview-footer-info').textContent =
                    `${rows.length} linha${rows.length !== 1 ? 's' : ''} · ${headers.length} coluna${headers.length !== 1 ? 's' : ''}`
            }

            function showLoading () {
                hideStates()
                document.getElementById('preview-loading').classList.remove('d-none')
            }

            function showEmpty (msg = 'Nenhum resultado encontrado.') {
                hideStates()
                const el = document.getElementById('preview-empty')
                el.querySelector('p').textContent = msg
                el.classList.remove('d-none')
            }

            function showError (msg) {
                hideStates()
                document.getElementById('preview-error').classList.remove('d-none')
                document.getElementById('preview-error-msg').textContent = msg
            }

            function hideStates () {
                ['preview-empty', 'preview-loading', 'preview-section', 'preview-error']
                    .forEach(id => document.getElementById(id).classList.add('d-none'))

                document.getElementById('preview-count-badge').classList.add('d-none')
            }

            async function exportPdf () {
                const cols = Array.from(document.querySelectorAll('input[name="cols"]:checked')).map(i => i.value)

                if (!meta || !cols.length) {
                    if (!meta) return setFieldError('model-select', 'Escolha o assunto do relatório.')

                    return setFieldError('columns-container', 'Marque pelo menos uma informação para exportar.')
                }

                clearFieldError('columns-container')

                const filters = Array.from(document.querySelectorAll('#filters-list .border')).map(row => ({
                    field: row.querySelector('.f-col').value,
                    operator: row.querySelector('.f-op').value,
                    value: row.querySelector('.f-val').value,
                })).filter(f => f.value !== '')

                try {
                    const res = await fetch(@json(route('reports.export.pdf')), {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: JSON.stringify({
                            source: meta.key,
                            columns: cols,
                            filters,
                            filter_related_values: document.getElementById('filter-related-values').checked,
                        }),
                    })

                    if (!res.ok) throw new Error('HTTP ' + res.status)

                    window.open(URL.createObjectURL(await res.blob()), '_blank')
                } catch {
                    toast('Erro ao gerar PDF.', 'danger')
                }
            }

            function getLabelForKey (key) {
                if (!meta) return key

                const normalized = key.replace(/__/g, '.')

                // 1) se o backend já definiu o rótulo exato, usa ele
                if (meta.columns && meta.columns[normalized]) {
                    return meta.columns[normalized]
                }

                // 2) fallback para colunas simples
                if (!normalized.includes('.')) {
                    return meta.columns?.[normalized] ?? normalized
                }

                // 3) fallback para relações
                const [rel, ...rest] = normalized.split('.')
                const r = addedRelations.find(r => r.name === rel) ?? (meta.relations ?? []).find(r => r.name === rel)

                if (rest[0] === 'pivot') {
                    return `${r?.label ?? rel} (vínculo) › ${r?.pivot?.columns?.[rest[1]] ?? rest[1]}`
                }

                return `${r?.label ?? rel} › ${r?.columns?.[rest.join('.')] ?? r?.columns?.[rest[0]] ?? rest.join('.')}`
            }

            function resetAll () {
                addedRelations = []
                document.getElementById('columns-container').innerHTML =
                    '<span class="text-muted small fst-italic">Escolha primeiro o assunto do relatório para ver as informações disponíveis.</span>'
                document.getElementById('added-relations-area').innerHTML = ''
                document.getElementById('relations-section').classList.add('d-none')
                document.getElementById('filters-list').innerHTML = ''
                document.getElementById('filter-related-values').checked = false
                window.__filterOpts = []
                document.getElementById('btn-run').setAttribute('disabled', true)
                hideStates()
                document.getElementById('preview-empty').classList.remove('d-none')
                clearFieldError('columns-container')
                clearFieldError('relation-select')
            }

            function setFieldError (fieldId, message) {
                const field = document.getElementById(fieldId)
                const feedback = document.getElementById(`${fieldId}-error`)

                field?.classList.add('is-invalid')

                if (feedback) {
                    feedback.textContent = message
                    feedback.classList.add('is-visible')
                }

                field?.focus()
            }

            function clearFieldError (fieldId) {
                const field = document.getElementById(fieldId)
                const feedback = document.getElementById(`${fieldId}-error`)

                field?.classList.remove('is-invalid')

                if (feedback) {
                    feedback.textContent = ''
                    feedback.classList.remove('is-visible')
                }
            }

            async function copyTable () {
                const headCells = Array.from(
                    document.querySelectorAll('#preview-head th')
                ).map(th => th.textContent.trim())

                const rows = Array.from(
                    document.querySelectorAll('#preview-body tr')
                ).map(tr =>
                    Array.from(tr.querySelectorAll('td'))
                        .map(td => td.textContent.trim())
                )

                const textLines = [
                    headCells.join('\t'),
                    ...rows.map(r => r.join('\t'))
                ].join('\n')

                let html = '<table border="1" style="border-collapse:collapse;">'

                html += '<thead><tr>'
                headCells.forEach(h => {
                    html += `<th style="padding:4px;">${esc(h)}</th>`
                })
                html += '</tr></thead>'

                html += '<tbody>'
                rows.forEach(r => {
                    html += '<tr>'
                    r.forEach(c => {
                        html += `<td style="padding:4px;">${esc(c)}</td>`
                    })
                    html += '</tr>'
                })
                html += '</tbody></table>'

                try {
                    if (navigator.clipboard?.write && window.ClipboardItem) {
                        await navigator.clipboard.write([
                            new ClipboardItem({
                                'text/html': new Blob([html], { type: 'text/html' }),
                                'text/plain': new Blob([textLines], { type: 'text/plain' })
                            })
                        ])

                        toast('Tabela copiada!', 'success')
                        return
                    }
                } catch (richErr) {
                    console.warn('Cópia rica indisponível neste navegador.', richErr)
                }

                try {
                    if (navigator.clipboard?.writeText) {
                        await navigator.clipboard.writeText(textLines)
                        toast('Tabela copiada!', 'success')
                        return
                    }
                } catch (textErr) {
                    console.warn('Cópia por writeText falhou.', textErr)
                }

                if (legacyCopyText(textLines)) {
                    toast('Tabela copiada!', 'success')
                    return
                }

                toast('Não foi possível copiar.', 'warning')
            }

            function legacyCopyText (text) {
                const textarea = document.createElement('textarea')
                textarea.value = text
                textarea.setAttribute('readonly', '')
                textarea.style.position = 'fixed'
                textarea.style.top = '-9999px'
                textarea.style.left = '-9999px'

                document.body.appendChild(textarea)
                textarea.focus()
                textarea.select()
                textarea.setSelectionRange(0, textarea.value.length)

                let copied = false

                try {
                    copied = document.execCommand('copy')
                } catch (err) {
                    console.warn('Fallback document.execCommand falhou.', err)
                }

                document.body.removeChild(textarea)

                return copied
            }

            function esc (str) {
                return ('' + str).replace(/[&<>"']/g, m =>
                    ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]))
            }

            function toast (msg, type = 'info') {
                let container = document.getElementById('toast-container')

                if (!container) {
                    container = document.createElement('div')
                    container.id = 'toast-container'
                    document.body.appendChild(container)
                }

                const el = document.createElement('div')
                const visualType = type === 'warning' ? 'info' : type
                const icon = visualType === 'success' ? 'fa-check-circle' :
                    visualType === 'danger' ? 'fa-exclamation-circle' : 'fa-info-circle'

                el.className = `toast-custom ${visualType}`
                el.setAttribute('role', visualType === 'danger' ? 'alert' : 'status')
                el.innerHTML = `
                    <div class="toast-content">
                        <i class="fas ${icon} fa-lg" aria-hidden="true"></i>
                        <div class="toast-body-text"></div>
                        <button type="button" class="btn-close-toast" aria-label="Fechar">×</button>
                    </div>
                    <div class="toast-progress"></div>`
                el.querySelector('.toast-body-text').textContent = msg
                el.querySelector('.btn-close-toast').addEventListener('click', () => el.remove())
                container.appendChild(el)
                requestAnimationFrame(() => el.classList.add('animate-slide-in'))
                setTimeout(() => {
                    el.classList.remove('animate-slide-in')
                    el.classList.add('animate-slide-out')
                    setTimeout(() => el.remove(), 400)
                }, 4000)
            }
        </script>
    @endpush

@endsection
