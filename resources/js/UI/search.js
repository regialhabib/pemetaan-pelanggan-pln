import {
    switchToMarkerMode,
    highlightMarker,
    getAllMarkers,
} from "../MapCore/markers.js";

const MIN_CHAR = 2;
const DEBOUNCE_DELAY = 250;

function debounce(fn, delay) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), delay);
    };
}

function highlightText(text, keyword) {
    if (!text || !keyword) return text || "";

    const escaped = keyword.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
    const regex = new RegExp(`(${escaped})`, "ig");

    return String(text).replace(regex, `<span class="search-highlight">$1</span>`);
}

/* ===============================
   INIT SEARCH (MULTI INPUT & RICH CARD)
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

        function renderResult(matches, rawKeyword) {
            clearResult();
            if (!matches.length) {
                const li = document.createElement("li");
                li.className = "search-empty";
                li.innerHTML = `
                    <div class="font-size-13">Tidak ada hasil untuk "<strong>${rawKeyword}</strong>"</div>
                `;
                resultBox.appendChild(li);
                resultBox.style.display = "block";
                return;
            }

            matches.slice(0, 8).forEach((marker) => {
                const p = marker.data;
                const li = document.createElement("li");
                li.className = "custom-search-item";
                li.innerHTML = `
                    <div class="text-dark font-size-13 text-truncate">
                        ${highlightText(p.nama, rawKeyword)}
                    </div>
                `;

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
            setTimeout(() => {
                marker.openPopup();
            }, 300);
        }

        const debouncedSearch = debounce(() => {
            const rawKeyword = input.value.trim();
            const keyword = rawKeyword.toLowerCase();

            clearBtn.style.display = keyword ? "block" : "none";

            if (keyword.length < MIN_CHAR) {
                clearResult();
                return;
            }

            // Pencarian multikriteria: Nama, IDPEL, Alamat, No HP
            const matches = getAllMarkers().filter((m) => {
                const p = m.data;
                const matchNama = p.nama && p.nama.toLowerCase().includes(keyword);
                const matchId = p.id_pelanggan && p.id_pelanggan.toLowerCase().includes(keyword);
                const matchAlamat = p.alamat && p.alamat.toLowerCase().includes(keyword);
                const matchHp = p.no_hp && p.no_hp.toLowerCase().includes(keyword);

                return matchNama || matchId || matchAlamat || matchHp;
            });

            renderResult(matches, rawKeyword);
        }, DEBOUNCE_DELAY);

        input.addEventListener("input", debouncedSearch);

        input.addEventListener("keydown", (e) => {
            const items = resultBox.querySelectorAll("li.custom-search-item");
            if (!items.length) return;

            if (e.key === "ArrowDown") {
                activeIndex = (activeIndex + 1) % items.length;
            } else if (e.key === "ArrowUp") {
                activeIndex = (activeIndex - 1 + items.length) % items.length;
            } else if (e.key === "Enter") {
                e.preventDefault();
                if (activeIndex >= 0 && items[activeIndex]) {
                    items[activeIndex].click();
                } else if (items[0]) {
                    items[0].click();
                }
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
