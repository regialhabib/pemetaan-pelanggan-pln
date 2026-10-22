import { focusMarker, getVisibleMarkers } from "../MapCore/markers.js";
import { escapeHtml } from "./html.js";

const MIN_CHAR = 2;
const DEBOUNCE_DELAY = 250;
const MAX_RESULTS = 8;

function debounce(fn, delay) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), delay);
    };
}

function highlightText(text, keyword) {
    const safeText = escapeHtml(text);
    const safeKeyword = escapeHtml(keyword);
    if (!safeText || !safeKeyword) return safeText;

    const pattern = safeKeyword.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");

    return safeText.replace(new RegExp(`(${pattern})`, "ig"), `<span class="search-highlight">$1</span>`);
}

/**
 * Pencarian pelanggan (nama, IDPEL, alamat, no HP) untuk setiap `.js-map-search` di halaman.
 */
export function initSearch() {
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

        function selectMarker(marker) {
            clearResult();
            focusMarker(marker);
        }

        function renderResult(matches, rawKeyword) {
            clearResult();

            if (!matches.length) {
                resultBox.innerHTML = `
                    <li class="search-empty">
                        <div class="font-size-13">Tidak ada hasil untuk "<strong>${escapeHtml(rawKeyword)}</strong>"</div>
                    </li>
                `;
            }

            matches.slice(0, MAX_RESULTS).forEach((marker) => {
                const li = document.createElement("li");
                li.className = "custom-search-item";
                li.innerHTML = `
                    <div class="text-dark font-size-13 text-truncate">
                        ${highlightText(marker.data.nama, rawKeyword)}
                    </div>
                `;
                li.addEventListener("click", () => selectMarker(marker));
                resultBox.appendChild(li);
            });

            resultBox.style.display = "block";
        }

        const debouncedSearch = debounce(() => {
            const rawKeyword = input.value.trim();
            const keyword = rawKeyword.toLowerCase();

            if (clearBtn) clearBtn.style.display = keyword ? "block" : "none";

            if (keyword.length < MIN_CHAR) {
                clearResult();
                return;
            }

            renderResult(
                getVisibleMarkers().filter((marker) => marker.searchText.includes(keyword)),
                rawKeyword,
            );
        }, DEBOUNCE_DELAY);

        input.addEventListener("input", debouncedSearch);

        input.addEventListener("keydown", (e) => {
            const items = resultBox.querySelectorAll("li.custom-search-item");
            if (!items.length) return;

            if (e.key === "Enter") {
                e.preventDefault();
                items[Math.max(activeIndex, 0)].click();
                return;
            }

            if (e.key === "ArrowDown") {
                activeIndex = (activeIndex + 1) % items.length;
            } else if (e.key === "ArrowUp") {
                activeIndex = (activeIndex - 1 + items.length) % items.length;
            } else {
                return;
            }

            items.forEach((li, i) => li.classList.toggle("active", i === activeIndex));
            items[activeIndex].scrollIntoView({ block: "nearest", behavior: "smooth" });
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
