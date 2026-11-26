<x-filament-widgets::widget>
    <x-filament::section heading="Perlu dilengkapi" description="Data yang masih kosong atau sudah lewat">
        @if (count($items) === 0)
            <p class="muphus-ok">
                <x-filament::icon icon="heroicon-o-check-circle" />
                Semua data sudah lengkap.
            </p>
        @else
            <ul class="muphus-todo">
                @foreach ($items as [$label, $count, $url])
                    <li>
                        <a href="{{ $url }}">
                            <span>{{ $label }}</span>
                            <span class="n">{{ $count }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>