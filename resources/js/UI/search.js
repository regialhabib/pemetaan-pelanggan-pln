import {
    switchToMarkerMode,
    highlightMarker,
    getAllMarkers,
} from "../MapCore/markers.js";
    
const MIN_CHAR = 3;
const DEBOUNCE_DELAY = 300;

function debounce(fn, delay) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), delay);
    };
}

function highlightText(text, keyword) {
    if (!keyword) return text;

    const escaped = keyword.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
    const regex = new RegExp(`(${escaped})`, "ig");

    return text.replace(regex, `<span class="search-highlight">$1</span>`);
}

/* ===============================
   INIT SEARCH (MULTI INPUT)
=============================== */
export function initSearch(map) {
    document.querySelectorAll(".js-map-search").forEach((wrapper) => {
        const input = wrapper.querySelector(".js-search-input");
        const resultBox = wrapper.querySelector(".js-search-result");
        const clearBtn = wrapper.querySelector(".js-clear");

        if (!input || !resultBox) return;

        let activeIndex = -1;

        function clearResult() {
            resultBox.innerHTML = "";
            resultBox.style.display = "none";
            activeIndex = -1;
        }

        function renderResult(matches) {
            clearResult();
            if (!matches.length) return;

            matches.slice(0, 8).forEach((marker) => {
                const li = document.createElement("li");
                li.innerHTML = highlightText(
                    marker.data.nama,
                    input.value.trim(),
                );

                li.addEventListener("click", () => selectMarker(marker));
                resultBox.appendChild(li);
            });

            resultBox.style.display = "block";
        }

        function selectMarker(marker) {
            clearResult();

            switchToMarkerMode();

            const latlng = marker.getLatLng();

            map.setView(latlng, 17, { animate: false });
            map.panTo(latlng, { animate: true, duration: 0.6 });

            highlightMarker(marker);
        }

        const debouncedSearch = debounce(() => {
            const keyword = input.value.trim().toLowerCase();

            clearBtn.style.display = keyword ? "block" : "none";

            if (keyword.length < MIN_CHAR) {
                clearResult();
                return;
            }

            const matches = getAllMarkers().filter((m) =>
                m.data.nama.toLowerCase().includes(keyword),
            );

            renderResult(matches);
        }, DEBOUNCE_DELAY);

        input.addEventListener("input", debouncedSearch);

        input.addEventListener("keydown", (e) => {
            const items = resultBox.querySelectorAll("li");
            if (!items.length) return;

            if (e.key === "ArrowDown") {
                activeIndex = (activeIndex + 1) % items.length;
            } else if (e.key === "ArrowUp") {
                activeIndex = (activeIndex - 1 + items.length) % items.length;
            } else if (e.key === "Enter") {
                e.preventDefault();
                items[Math.max(activeIndex, 0)].click();
                return;
            } else {
                return;
            }

            items.forEach((li) => li.classList.remove("active"));
            items[activeIndex].classList.add("active");

            items[activeIndex].scrollIntoView({
                block: "nearest",
                behavior: "smooth",
            });
        });

        clearBtn?.addEventListener("click", () => {
            input.value = "";
            clearBtn.style.display = "none";
            clearResult();
            input.focus();
        });

        document.addEventListener("click", (e) => {
            if (!wrapper.contains(e.target)) {
                clearResult();
            }
        });
    });
}
