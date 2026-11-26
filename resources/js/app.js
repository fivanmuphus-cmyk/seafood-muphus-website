// Menu seluler
const toggle = document.getElementById('menu-toggle');
const menu = document.getElementById('mobile-menu');
if (toggle && menu) {
    toggle.addEventListener('click', () => {
        const open = menu.classList.toggle('hidden') === false;
        toggle.setAttribute('aria-expanded', String(open));
    });
}

// Label nama cabang di samping pin peta (klik = petunjuk arah)
const setMapLabel = (root, item, hasMap) => {
    const label = root.querySelector('[data-map-label]');
    if (!label) return;
    const show = Boolean(hasMap && item.dataset.directions && item.dataset.ownLabel === '1');
    label.classList.toggle('hidden', !show);
    if (show) {
        label.href = item.dataset.directions;
        label.querySelector('[data-map-label-text]').textContent = item.dataset.name;
    }
};

// Peta digeser/diperbesar: pin tidak lagi di tengah, jadi label disembunyikan.
window.addEventListener('blur', () => {
    setTimeout(() => {
        const active = document.activeElement;
        if (active && active.tagName === 'IFRAME') {
            active.parentElement?.querySelector('[data-map-label]')?.classList.add('hidden');
        }
    }, 0);
});

// Akordeon cabang + peta
document.querySelectorAll('[data-branches]').forEach((wrap) => {
    const items = wrap.querySelectorAll('[data-branch]');
    const map = wrap.querySelector('[data-branch-map]');
    const emptyBox = wrap.querySelector('[data-map-empty]');

    // Tampilkan peta cabang yang dibuka; bila belum punya peta, tampilkan pesan.
    const showMap = (item) => {
        const url = item.dataset.map;
        if (map && url) map.src = url;
        emptyBox?.classList.toggle('hidden', Boolean(url && map));
        setMapLabel(wrap, item, Boolean(url && map));
    };

    const setState = (item, open) => {
        item.querySelector('[data-branch-toggle]').setAttribute('aria-expanded', String(open));
        item.querySelector('[data-branch-panel]').classList.toggle('hidden', !open);
        item.querySelector('[data-icon]').textContent = open ? '−' : '+';
    };

    items.forEach((item) => {
        item.querySelector('[data-branch-toggle]').addEventListener('click', () => {
            const opening = item.querySelector('[data-branch-panel]').classList.contains('hidden');
            items.forEach((other) => setState(other, false));
            if (opening) {
                setState(item, true);
                showMap(item);
            }
        });
    });

    // Cabang terdekat: bila pengunjung mengizinkan lokasi, tampilkan 3 cabang terdekat.
    // Lokasi hanya dipakai di browser pengunjung dan tidak dikirim ke server.
    const list = wrap.querySelector('[data-branch-list]');
    const note = wrap.querySelector('[data-near-note]');
    const withPos = [...items].filter((item) => item.dataset.lat && item.dataset.lng);
    if (!list || !withPos.length || items.length <= 3 || !('geolocation' in navigator)) return;

    const km = (lat1, lng1, lat2, lng2) => {
        const rad = (d) => (d * Math.PI) / 180;
        const a = Math.sin(rad(lat2 - lat1) / 2) ** 2
            + Math.cos(rad(lat1)) * Math.cos(rad(lat2)) * Math.sin(rad(lng2 - lng1) / 2) ** 2;
        return 6371 * 2 * Math.asin(Math.sqrt(a));
    };

    navigator.geolocation.getCurrentPosition((pos) => {
        const { latitude, longitude } = pos.coords;
        const ranked = [...items].map((item) => ({
            item,
            d: item.dataset.lat ? km(latitude, longitude, Number(item.dataset.lat), Number(item.dataset.lng)) : Infinity,
        })).sort((a, b) => a.d - b.d);

        ranked.forEach(({ item, d }, i) => {
            list.appendChild(item);
            const show = i < 3;
            item.classList.toggle('hidden', !show);
            setState(item, false);
            const num = item.querySelector('[data-branch-toggle] > span:first-child');
            if (num) num.textContent = String(i + 1).padStart(2, '0');
            const label = item.querySelector('[data-distance]');
            if (label) {
                const known = show && Number.isFinite(d);
                label.classList.toggle('hidden', !known);
                if (known) label.textContent = d < 1 ? 'Kurang dari 1 km dari Anda' : `± ${Math.round(d)} km dari Anda`;
            }
        });

        note?.classList.remove('hidden');
        const first = ranked[0].item;
        setState(first, true);
        showMap(first);
    }, () => {}, { timeout: 8000, maximumAge: 600000 });
});

// Perbesar brosur
const dialog = document.getElementById('lightbox');
const dialogImg = document.getElementById('lightbox-img');
document.addEventListener('click', (event) => {
    const trigger = event.target.closest('[data-lightbox]');
    if (trigger && dialog) {
        dialogImg.src = trigger.dataset.lightbox;
        dialogImg.alt = trigger.dataset.alt || '';
        dialog.showModal();
    }
});
dialog?.addEventListener('click', (event) => {
    if (event.target === dialog) dialog.close();
});

// Saring menu + tombol "Lihat lebih banyak menu"
document.querySelectorAll('[data-menu]').forEach((root) => {
    const PAGE = 8;
    const cards = [...root.querySelectorAll('[data-card]')];
    const chips = [...root.querySelectorAll('[data-filter]')];
    const more = root.querySelector('[data-more]');
    let filter = 'all';
    let limit = PAGE;

    const render = () => {
        let shown = 0;
        let matching = 0;

        cards.forEach((card) => {
            const ok = filter === 'all' || card.dataset.category === filter;
            if (ok) matching++;
            const visible = ok && shown < limit;
            if (visible) shown++;
            card.classList.toggle('hidden', !visible);
        });

        more?.classList.toggle('hidden', matching <= limit);
        chips.forEach((chip) => chip.setAttribute('aria-pressed', String(chip.dataset.filter === filter)));
    };

    chips.forEach((chip) => chip.addEventListener('click', () => {
        filter = chip.dataset.filter;
        limit = PAGE;
        render();
    }));
    more?.addEventListener('click', () => {
        limit += PAGE;
        render();
    });

    render();
});

// Halaman Cabang: pencarian, filter kota, akordeon, dan peta
document.querySelectorAll('[data-finder]').forEach((root) => {
    const items = [...root.querySelectorAll('[data-branch]')];
    if (!items.length) return;

    const chips = [...root.querySelectorAll('[data-city-filter]')];
    const extra = [...root.querySelectorAll('[data-extra-city]')];
    const moreBtn = root.querySelector('[data-more-cities]');
    const input = root.querySelector('[data-search]');
    const count = root.querySelector('[data-count]');
    const empty = root.querySelector('[data-empty]');
    const map = root.querySelector('iframe[data-map]');
    const emptyBox = root.querySelector('[data-map-empty]');
    let city = 'all';

    const panelOf = (item) => item.querySelector('[data-panel]');

    const setOpen = (item, open) => {
        item.querySelector('[data-toggle]').setAttribute('aria-expanded', String(open));
        panelOf(item).classList.toggle('hidden', !open);
        item.querySelector('[data-icon]').textContent = open ? '−' : '+';
    };

    const openOnly = (item) => {
        items.forEach((other) => setOpen(other, false));
        setOpen(item, true);
        const url = item.dataset.map;
        if (map && url) map.src = url;
        emptyBox?.classList.toggle('hidden', Boolean(url && map));
        setMapLabel(root, item, Boolean(url && map));
    };

    const render = () => {
        const q = (input?.value || '').trim().toLowerCase();
        let n = 0;
        let firstVisible = null;
        let firstWithMap = null;

        items.forEach((item) => {
            const ok = (city === 'all' || item.dataset.city === city) && (!q || item.dataset.search.includes(q));
            item.classList.toggle('hidden', !ok);
            if (ok) {
                n++;
                item.querySelector('[data-number]').textContent = String(n).padStart(2, '0');
                firstVisible ??= item;
                if (item.dataset.map) firstWithMap ??= item;
            }
        });

        if (count) count.textContent = `Menampilkan ${n} cabang`;
        empty?.classList.toggle('hidden', n !== 0);

        const stillOpen = items.some((item) => !item.classList.contains('hidden') && !panelOf(item).classList.contains('hidden'));
        if (!stillOpen && firstVisible) openOnly(firstWithMap ?? firstVisible);
    };

    items.forEach((item) => {
        item.querySelector('[data-toggle]').addEventListener('click', () => {
            panelOf(item).classList.contains('hidden') ? openOnly(item) : setOpen(item, false);
        });
    });

    chips.forEach((chip) => chip.addEventListener('click', () => {
        city = chip.dataset.cityFilter;
        chips.forEach((c) => c.setAttribute('aria-pressed', String(c === chip)));
        render();
    }));

    input?.addEventListener('input', render);

    moreBtn?.addEventListener('click', () => {
        const show = extra[0]?.classList.contains('hidden');
        extra.forEach((e) => e.classList.toggle('hidden', !show));
        moreBtn.querySelector('[data-more-label]').textContent = show ? 'Lebih sedikit' : 'Lainnya';
        moreBtn.querySelector('[data-more-icon]').textContent = show ? '−' : '+';
    });

    render();
});

// Detail artikel: salin tautan
document.querySelectorAll('[data-copy-link]').forEach((button) => {
    button.addEventListener('click', async () => {
        const label = button.textContent;
        try {
            await navigator.clipboard.writeText(button.dataset.copyLink);
            button.textContent = 'Tautan disalin';
        } catch {
            window.prompt('Salin tautan ini:', button.dataset.copyLink);
        }
        setTimeout(() => { button.textContent = label; }, 2000);
    });
});

// Halaman Karir: filter Outlet / Office
document.querySelectorAll('[data-careers]').forEach((root) => {
    const chips = [...root.querySelectorAll('[data-career-filter]')];
    const groups = [...root.querySelectorAll('[data-career-group]')];

    const apply = (filter) => {
        groups.forEach((group) => {
            group.classList.toggle('hidden', filter !== 'all' && group.dataset.careerGroup !== filter);
        });
        chips.forEach((chip) => chip.setAttribute('aria-pressed', String(chip.dataset.careerFilter === filter)));

        const url = new URL(window.location.href);
        filter === 'all' ? url.searchParams.delete('jenis') : url.searchParams.set('jenis', filter);
        window.history.replaceState(null, '', url);
    };

    chips.forEach((chip) => chip.addEventListener('click', () => apply(chip.dataset.careerFilter)));
});

// Akordeon FAQ (satu terbuka sekaligus)
document.querySelectorAll('[data-faq]').forEach((list) => {
    const items = [...list.querySelectorAll('[data-faq-item]')];

    const setOpen = (item, open) => {
        const toggle = item.querySelector('[data-faq-toggle]');
        toggle.setAttribute('aria-expanded', String(open));
        toggle.classList.toggle('text-brand-600', open);
        toggle.classList.toggle('text-ink-900', !open);
        item.querySelector('[data-faq-panel]').classList.toggle('hidden', !open);
        item.querySelector('[data-faq-icon]').textContent = open ? '×' : '+';
    };

    items.forEach((item) => {
        item.querySelector('[data-faq-toggle]').addEventListener('click', () => {
            const opening = item.querySelector('[data-faq-panel]').classList.contains('hidden');
            items.forEach((other) => setOpen(other, false));
            if (opening) setOpen(item, true);
        });
    });
});

// Halaman Menu: saring kategori dan subkategori, kartu andalan, daftar lengkap
document.querySelectorAll('[data-menu-page]').forEach((root) => {
    const PAGE = 8;
    const catChips = [...root.querySelectorAll('[data-cat]')];
    const subChips = [...root.querySelectorAll('[data-sub-filter]')];
    const subBar = root.querySelector('[data-sub-bar]');
    const items = [...root.querySelectorAll('[data-item]')];
    const sections = [...root.querySelectorAll('[data-section]')];
    const more = root.querySelector('[data-more-cards]');
    const empty = root.querySelector('[data-empty]');
    let cat = 'all';
    let sub = 'all';
    let limit = PAGE;

    // Daftar lengkap: terbuka di layar lebar, tertutup (akordeon) di HP.
    const wide = window.matchMedia('(min-width: 1024px)').matches;
    root.querySelectorAll('[data-detail]').forEach((d) => { d.open = wide; });

    const render = () => {
        subBar?.classList.toggle('hidden', cat !== 'seafood');
        subBar?.classList.toggle('flex', cat === 'seafood');

        let cardShown = 0;
        let cardMatching = 0;

        items.forEach((el) => {
            const okCat = cat === 'all' || el.dataset.category === cat;
            const okSub = cat !== 'seafood' || sub === 'all' || el.dataset.sub === sub;
            let visible = okCat && okSub;

            if (el.dataset.kind === 'card') {
                if (visible) cardMatching++;
                visible = visible && cardShown < limit;
                if (visible) cardShown++;
            }

            el.classList.toggle('hidden', !visible);
        });

        more?.classList.toggle('hidden', cardMatching <= limit);
        sections.forEach((sec) => sec.classList.toggle('hidden', !sec.querySelector('[data-item]:not(.hidden)')));
        empty?.classList.toggle('hidden', items.some((el) => !el.classList.contains('hidden')));

        catChips.forEach((c) => c.setAttribute('aria-pressed', String(c.dataset.cat === cat)));
        subChips.forEach((c) => c.setAttribute('aria-pressed', String(c.dataset.subFilter === sub)));
    };

    catChips.forEach((chip) => chip.addEventListener('click', () => {
        cat = chip.dataset.cat;
        sub = 'all';
        limit = PAGE;
        render();
    }));
    subChips.forEach((chip) => chip.addEventListener('click', () => {
        sub = chip.dataset.subFilter;
        limit = PAGE;
        render();
    }));
    more?.addEventListener('click', () => {
        limit += PAGE;
        render();
    });

    render();
});



// Layar muat: tampil sekali per sesi, lalu hilang setelah halaman siap.
(() => {
    const preloader = document.getElementById('preloader');
    const root = document.documentElement;

    if (!preloader || !root.classList.contains('has-preloader')) {
        preloader?.remove();
        return;
    }

    const MIN_VISIBLE = 600;   // ms: supaya animasi tidak berkedip
    const MAX_VISIBLE = 4000;  // ms: batas aman bila ada berkas yang lambat
    const shownAt = performance.now();
    let closed = false;

    root.classList.add('preloader-lock');

    const close = () => {
        if (closed) return;
        closed = true;
        try { sessionStorage.setItem('muphus:splash', '1'); } catch (e) {}
        preloader.classList.add('is-done');
        root.classList.remove('preloader-lock');
        setTimeout(() => preloader.remove(), 700);
    };

    const ready = () => setTimeout(close, Math.max(0, MIN_VISIBLE - (performance.now() - shownAt)));

    if (document.readyState === 'complete') ready();
    else window.addEventListener('load', ready, { once: true });

    setTimeout(close, MAX_VISIBLE);
})();

// Garis kemajuan di atas layar saat berpindah halaman.
(() => {
    const bar = document.getElementById('nav-progress');
    if (!bar) return;

    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[href]');
        if (!link || event.defaultPrevented || event.button !== 0) return;
        if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
        if ((link.target && link.target !== '_self') || link.hasAttribute('download')) return;

        const url = new URL(link.href, window.location.href);
        if (url.origin !== window.location.origin) return;
        if (url.pathname === window.location.pathname && url.search === window.location.search) return;

        bar.classList.add('is-active');
    });

    // Tombol Kembali memulihkan halaman dari cache: matikan garisnya.
    window.addEventListener('pageshow', () => bar.classList.remove('is-active'));
})();