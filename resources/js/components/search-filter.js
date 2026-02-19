async function fetchFilteredResults(element) {
    const url = element.dataset.url;
    const target = element.dataset.target;

    const wrapper = element.closest(".search-wrapper");

    const searchInput = wrapper?.querySelector(".realtime-filter");
    const semesterSelect = wrapper?.querySelector(".semester-filter");

    const params = new URLSearchParams();

    if (searchInput?.value) params.append("q", searchInput.value);
    if (semesterSelect?.value) params.append("semester", semesterSelect.value);

    const response = await fetch(`${url}?${params}`, {
        headers: { "X-Requested-With": "XMLHttpRequest" }
    });

    const html = await response.text();

    document.querySelector(target).innerHTML = html;
}

document.addEventListener("input", (e) => {
    if (!e.target.classList.contains("realtime-filter")) return;
    fetchFilteredResults(e.target);
});

document.addEventListener("change", (e) => {
    if (!e.target.classList.contains("semester-filter")) return;
    fetchFilteredResults(e.target);
});
