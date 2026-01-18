import {
    switchToMarkerMode,
    highlightMarker,
    getAllMarkers,
} from "../Map/markers.js";

const MIN_CHAR = 3;
const DEBOUNCE_DELAY = 300;

function debounce(fn, delay) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => {
            fn.apply(this, args);
        }, delay);
    };
}

function highlightText(text, keyword) {
    if (!keyword) return text;

    const escaped = keyword.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
    const regex = new RegExp(`(${escaped})`, "ig");

    return text.replace(regex, `<span class="search-highlight">$1</span>`);
}

export function initSearch() {
    const input = document.getElementById("searchPelanggan");
    const resultBox = document.getElementById("search-result");
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

        matches.slice(0, 8).forEach((marker, index) => {
            const li = document.createElement("li");
            li.className = "list-group-item";
            li.innerHTML = highlightText(marker.data.nama, input.value.trim());

            li.addEventListener("click", () => {
                selectMarker(marker);
            });

            resultBox.appendChild(li);
        });

        resultBox.style.display = "block";
    }

    function selectMarker(marker) {
        clearResult();

        switchToMarkerMode();

        const map = marker._map;
        const latlng = marker.getLatLng();
        const NAV_ZOOM = 17;

        //  RESET view (WAJIB)
        map.setView(latlng, NAV_ZOOM, {
            animate: false,
        });

        // 2 PAN ulang (agar tetap halus)
        map.panTo(latlng, {
            animate: true,
            duration: 0.6,
        });
        highlightMarker(marker);
    }

    // input.addEventListener("input", () => {
    //     const keyword = input.value.trim().toLowerCase();
    //     if (!keyword) {
    //         clearResult();
    //         return;
    //     }

    //     const markers = getAllMarkers();

    //     const matches = markers.filter((m) =>
    //         m.data.nama.toLowerCase().includes(keyword)
    //     );

    //     renderResult(matches);
    // });

    const debouncedSearch = debounce(() => {
        const keyword = input.value.trim().toLowerCase();

        //  minimum character
        if (keyword.length < MIN_CHAR) {
            clearResult();
            return;
        }

        const markers = getAllMarkers();

        const matches = markers.filter((m) =>
            m.data.nama.toLowerCase().includes(keyword)
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

            //  jika belum ada navigasi keyboard → pilih item pertama
            if (activeIndex === -1 && items.length > 0) {
                items[0].click();
                return;
            }

            //  jika sudah navigasi keyboard
            if (activeIndex >= 0) {
                items[activeIndex].click();
            }

            return;
        } else {
            return;
        }

        items.forEach((li) => li.classList.remove("active"));
        items[activeIndex].classList.add("active");
    });

    // klik di luar → tutup
    document.addEventListener("click", (e) => {
        if (!e.target.closest("#map-search")) {
            clearResult();
        }
    });
}
