{{-- Tampilan admin Muphus: bersih ala SaaS (latar lembut, kartu putih, sidebar ringkas). Tanpa build tambahan. --}}
<style>
    :root { --m-bg: #f6f8f6; --m-line: #e5eae5; --m-soft: #eef7ef; --m-muted: #5b655b; }

    .fi-body { background-color: var(--m-bg); }

    /* Sidebar & bilah atas */
    .fi-sidebar { background-color: #fff; border-right: 1px solid var(--m-line); }
    .fi-topbar > nav { background-color: #fff; border-bottom: 1px solid var(--m-line); box-shadow: none; }
    .fi-sidebar-item-btn { border-radius: .5rem; }
    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn { background-color: var(--m-soft); }
    .fi-sidebar-group-label {
        font-size: .6875rem; font-weight: 600; letter-spacing: .06em; text-transform: uppercase; color: var(--m-muted);
    }

    /* Kartu */
    .fi-section, .fi-ta-ctn, .fi-wi-stats-overview-stat, .fi-fo-field-wrp .fi-input-wrp { border-radius: .75rem; }
    .fi-section, .fi-ta-ctn, .fi-wi-stats-overview-stat { box-shadow: 0 1px 2px rgba(12, 52, 20, .05); }

    /* Judul halaman */
    .fi-header-heading { font-weight: 600; letter-spacing: -.01em; }
    .fi-btn { border-radius: .625rem; }

    /* Tabel: kepala kecil, baris menyorot saat disentuh, pesan baru bertanda hijau */
    .fi-ta-header-cell-label { font-size: .75rem; font-weight: 600; letter-spacing: .03em; text-transform: uppercase; color: var(--m-muted); }
    .fi-ta-row:hover { background-color: #f8faf8; }
    .fi-ta-row.muphus-unread { box-shadow: inset 3px 0 0 #238737; }
    .fi-ta-row.muphus-unread .fi-ta-text-item { font-weight: 600; }

    /* Kartu pintasan & data belum lengkap di dashboard */
    .muphus-quick { display: grid; gap: .5rem; grid-template-columns: repeat(auto-fit, minmax(11rem, 1fr)); }
    .muphus-quick a {
        display: flex; align-items: center; gap: .625rem; padding: .75rem .875rem;
        border: 1px solid var(--m-line); border-radius: .625rem; background: #fff;
        font-size: .875rem; font-weight: 500; color: #142015; text-decoration: none; transition: border-color .15s, background-color .15s;
    }
    .muphus-quick a:hover { border-color: #238737; background-color: var(--m-soft); }
    .muphus-quick svg { width: 1.25rem; height: 1.25rem; color: #238737; flex-shrink: 0; }
    .muphus-todo { list-style: none; margin: 0; padding: 0; display: grid; gap: .25rem; }
    .muphus-todo a {
        display: flex; align-items: center; justify-content: space-between; gap: 1rem;
        padding: .5rem .625rem; border-radius: .5rem; font-size: .875rem; color: #142015; text-decoration: none;
    }
    .muphus-todo a:hover { background-color: var(--m-soft); }
    .muphus-todo .n { min-width: 1.5rem; padding: .0625rem .5rem; border-radius: 999px; background: #fef3c7; color: #92400e; font-size: .75rem; font-weight: 600; text-align: center; }
    .muphus-ok { display: flex; align-items: center; gap: .5rem; font-size: .875rem; color: #166534; }
    .muphus-ok svg { width: 1.25rem; height: 1.25rem; }
</style>