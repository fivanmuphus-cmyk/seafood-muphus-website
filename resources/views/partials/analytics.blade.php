@php
    $gaId = (string) config('muphus.analytics.id');
@endphp
@if (preg_match('/^G-[A-Z0-9]{4,15}$/', $gaId))
    <div id="consent-banner" hidden role="dialog" aria-live="polite" aria-label="Persetujuan cookie"
         class="fixed inset-x-0 bottom-0 z-50 border-t border-line bg-paper px-5 py-5 shadow-[0_-4px_16px_rgba(0,0,0,.08)] md:px-8">
        <div class="mx-auto flex max-w-[1200px] flex-col gap-4 md:flex-row md:items-center md:justify-between md:gap-8">
            <p class="text-sm leading-6 text-ink-700">
                Kami memakai Google Analytics untuk menghitung jumlah pengunjung dan memahami halaman yang paling sering dibuka, supaya website ini terus membaik. Data ini hanya dikumpulkan bila Anda setuju.
                <a href="/kebijakan-privasi" class="underline hover:text-brand-600">Kebijakan Privasi</a>
            </p>
            <div class="flex shrink-0 gap-3">
                <button type="button" data-consent="denied" class="border border-ink-900 px-5 py-2.5 text-xs font-medium uppercase tracking-[.14em] text-ink-900 hover:bg-ink-900 hover:text-white">Tolak</button>
                <button type="button" data-consent="granted" class="border border-brand-900 bg-brand-900 px-5 py-2.5 text-xs font-medium uppercase tracking-[.14em] text-white hover:bg-brand-600">Terima</button>
            </div>
        </div>
    </div>
    <script>
        (function () {
            var ID = @json($gaId), KEY = 'muphus:analytics';
            var box = document.getElementById('consent-banner');

            function load() {
                if (window.__gaLoaded) return;
                window.__gaLoaded = true;
                window.dataLayer = window.dataLayer || [];
                window.gtag = function () { dataLayer.push(arguments); };
                gtag('js', new Date());
                gtag('config', ID, { anonymize_ip: true });
                var s = document.createElement('script');
                s.async = true;
                s.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(ID);
                document.head.appendChild(s);
            }

            function stored() { try { return localStorage.getItem(KEY); } catch (e) { return null; } }

            var v = stored();
            if (v === 'granted') load();
            else if (v !== 'denied') box.hidden = false;

            box.querySelectorAll('[data-consent]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var ok = btn.getAttribute('data-consent') === 'granted';
                    try { localStorage.setItem(KEY, ok ? 'granted' : 'denied'); } catch (e) {}
                    box.hidden = true;
                    if (ok) load();
                });
            });

            // Tautan "Pengaturan cookie" di footer: tampilkan lagi banner agar pilihan bisa diubah.
            document.querySelectorAll('[data-consent-reset]').forEach(function (el) {
                el.addEventListener('click', function () { box.hidden = false; });
            });
        })();
    </script>
@endif
