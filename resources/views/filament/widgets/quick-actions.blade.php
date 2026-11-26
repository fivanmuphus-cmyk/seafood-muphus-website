<x-filament-widgets::widget>
    <x-filament::section heading="Pintasan" description="Tugas yang paling sering dilakukan">
        <div class="muphus-quick">
            @foreach ($links as [$label, $url, $icon])
                <a href="{{ $url }}">
                    <x-filament::icon :icon="$icon" />
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>