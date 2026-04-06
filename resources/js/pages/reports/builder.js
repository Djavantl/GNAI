let meta = {};
let currentBase = null;
let addedRelated = [];
let reachable = [];
let adjacency = {};

document.addEventListener("DOMContentLoaded", init);

async function init() {

    const res = await fetch(window.routes.reportsMeta);
    meta = await res.json();

    adjacency = buildAdjacency();

    const baseSelect = document.getElementById("baseSelect");

    Object.keys(meta.tables).forEach(t => {
        const opt = new Option(meta.tables[t].label + " (" + t + ")", t);
        baseSelect.add(opt);
    });

    baseSelect.addEventListener("change", onBaseChange);
    document.getElementById("relatedSelect").addEventListener("change", onRelatedAdd);

    document.getElementById("btnAddFilter").addEventListener("click", addFilterRow);
    document.getElementById("btnPreview").addEventListener("click", runPreview);
    document.getElementById("btnExportPdf").addEventListener("click", exportPdf);

    if (baseSelect.options.length) {
        baseSelect.selectedIndex = 0;
        onBaseChange();
    }

}

function buildAdjacency() {

    const adj = {};

    Object.keys(meta.relations).forEach(k => {

        const [a, b] = k.split(".");

        adj[a] = adj[a] || new Set();
        adj[b] = adj[b] || new Set();

        adj[a].add(b);
        adj[b].add(a);

    });

    const out = {};
    Object.keys(adj).forEach(k => out[k] = Array.from(adj[k]));

    return out;

}

function findPath(base, target) {

    if (base === target) return [base];

    const queue = [[base]];
    const visited = new Set([base]);

    while (queue.length) {

        const path = queue.shift();
        const last = path[path.length - 1];
        const neighbors = adjacency[last] || [];

        for (const n of neighbors) {

            if (visited.has(n)) continue;

            const newPath = path.concat([n]);

            if (n === target) return newPath;

            visited.add(n);
            queue.push(newPath);

        }

    }

    return null;

}

function findReachable(base) {

    const visited = new Set([base]);
    const queue = [base];

    while (queue.length) {

        const cur = queue.shift();
        const neighbors = adjacency[cur] || [];

        for (const n of neighbors) {

            if (!visited.has(n)) {

                visited.add(n);
                queue.push(n);

            }

        }

    }

    return Array.from(visited);

}

function onBaseChange() {

    currentBase = document.getElementById("baseSelect").value;

    addedRelated = [];
    reachable = findReachable(currentBase);

    populateRelatedOptions();
    renderColumns();

    document.getElementById("filtersList").innerHTML = "";

}

function populateRelatedOptions() {

    const sel = document.getElementById("relatedSelect");

    sel.innerHTML = '<option value="">-- Selecionar (opcional) --</option>';

    reachable.forEach(t => {

        if (t === currentBase) return;

        const isPivot = meta.tables[t]?.pivot;
        if (isPivot) return;

        const opt = new Option(meta.tables[t].label + " (" + t + ")", t);
        sel.appendChild(opt);

    });

}

function onRelatedAdd() {

    const sel = document.getElementById("relatedSelect");
    const val = sel.value;

    if (!val) return;

    if (!addedRelated.includes(val)) {
        addedRelated.push(val);
    }

    sel.selectedIndex = 0;
    renderColumns();

}

function renderColumns() {

    const container = document.getElementById("columnsContainer");
    container.innerHTML = "";

    renderGroupWithImplicitRelated(container, currentBase);

    for (const t of addedRelated) {
        renderGroupWithPath(container, t);
    }

}

function createColumnHTML(table, column) {

    const full = `${table}.${column}`;
    const id = `chk_${table}__${column}`;

    return `
    <div class="form-check d-flex align-items-center me-2">
        <input class="form-check-input col-checkbox" type="checkbox" id="${id}" value="${full}">
        <label class="form-check-label ms-1 me-2" for="${id}">${column}</label>
        <input type="text"
               class="form-control form-control-sm col-label-input"
               data-col="${full}"
               placeholder="rótulo"
               style="width:140px;">
    </div>
    `;

}

function renderGroupWithImplicitRelated(container, table) {

    const baseLabel = meta.tables[table].label || table;

    const baseHtml = document.createElement("div");
    baseHtml.className = "mb-3";

    baseHtml.innerHTML =
        `<strong>${baseLabel} <small class="text-muted">(${table})</small></strong>
        <div class="d-flex flex-wrap gap-2 mt-1" id="cols_${table}"></div>`;

    container.appendChild(baseHtml);

    const baseInner = baseHtml.querySelector(`#cols_${table}`);

    (meta.tables[table].columns || []).forEach(c => {
        baseInner.insertAdjacentHTML("beforeend", createColumnHTML(table, c));
    });

}

function renderGroupWithPath(container, target) {

    const path = findPath(currentBase, target);
    if (!path) return;

    const targetLabel = meta.tables[target].label || target;

    const group = document.createElement("div");
    group.className = "mb-3";

    group.innerHTML =
        `<strong>${targetLabel} <small class="text-muted">(${target})</small></strong>
        <div class="d-flex flex-wrap gap-2 mt-1" id="cols_${target}"></div>`;

    container.appendChild(group);

    const inner = group.querySelector(`#cols_${target}`);

    (meta.tables[target].columns || []).forEach(c => {
        inner.insertAdjacentHTML("beforeend", createColumnHTML(target, c));
    });

}

function addFilterRow() {

    const allCols = Array.from(document.querySelectorAll(".col-checkbox"))
        .map(cb => cb.value);

    const div = document.createElement("div");
    div.className = "d-flex gap-2 mb-2 filter-row";

    div.innerHTML = `
        <select class="form-select form-select-sm filter-col w-25">
            ${allCols.map(c => `<option value="${c}">${c}</option>`).join("")}
        </select>
        <select class="form-select form-select-sm filter-op w-25">
            <option value="=">=</option>
            <option value="!=">!=</option>
            <option value=">">></option>
            <option value="<"><</option>
            <option value="like">Contém</option>
        </select>
        <input class="form-control form-control-sm filter-val">
        <button class="btn btn-sm btn-danger remove-filter">X</button>
    `;

    div.querySelector(".remove-filter").addEventListener("click", () => div.remove());

    document.getElementById("filtersList").appendChild(div);

}

function buildPayload(forExport = false) {

    const selectedCols = Array.from(
        document.querySelectorAll(".col-checkbox:checked")
    ).map(cb => cb.value);

    const select = selectedCols.length
        ? selectedCols
        : (meta.tables[currentBase].columns || []).map(c => `${currentBase}.${c}`);

    const filters = Array.from(document.querySelectorAll(".filter-row"))
        .map(row => ({
            column: row.querySelector(".filter-col").value,
            operator: row.querySelector(".filter-op").value,
            value: row.querySelector(".filter-val").value
        }));

    const labels = {};

    document.querySelectorAll(".col-label-input").forEach(inp => {

        const col = inp.dataset.col;
        const val = (inp.value || "").trim();

        if (val) labels[col] = val;

    });

    return {
        base: currentBase,
        select,
        joins: addedRelated,
        filters,
        labels,
        report_name: document.getElementById("reportName").value || null,
        group_by_base: document.getElementById("groupByBase").checked,
        limit: forExport ? "all" : parseInt(document.getElementById("limitInput").value || 200)
    };

}

async function runPreview() {

    const payload = buildPayload(false);

    const res = await fetch(window.routes.reportsRun, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": window.csrfToken
        },
        body: JSON.stringify(payload)
    });

    const json = await res.json();
    renderPreview(json.data ?? json, json.total_count ?? 0);

}

function renderPreview(rows, totalCount) {

    const div = document.getElementById("previewArea");

    if (!rows || rows.length === 0) {
        div.innerHTML = '<div class="alert alert-warning">Nenhum resultado</div>';
        return;
    }

    const headers = Object.keys(rows[0]);

    let html = `<div class="mb-2"><strong>Total encontrado:</strong> ${totalCount}</div>`;
    html += '<table class="table table-sm table-striped"><thead><tr>';

    headers.forEach(h => html += `<th>${h.replace(/__/g,'.')}</th>`);

    html += '</tr></thead><tbody>';

    rows.forEach(r => {

        html += "<tr>";
        headers.forEach(h => html += `<td>${r[h] ?? ""}</td>`);
        html += "</tr>";

    });

    html += "</tbody></table>";

    div.innerHTML = html;

}

function exportPdf() {

    const payload = buildPayload(true);

    if (payload.select.length > 20) {
        if (!confirm("Muitas colunas selecionadas. O PDF pode quebrar. Deseja continuar?")) {
            return;
        }
    }

    const f = document.createElement("form");
    f.method = "POST";
    f.action = window.routes.reportsExportPdf;
    f.style.display = "none";

    f.innerHTML = `
        <input name="_token" value="${window.csrfToken}">
        <input name="payload" value='${JSON.stringify(payload)}'>
    `;

    document.body.appendChild(f);
    f.submit();
    f.remove();

}