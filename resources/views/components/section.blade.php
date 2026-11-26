@props(['id' => null])
<section @if ($id) id="{{ $id }}" @endif {{ $attributes->class(['py-[72px] lg:py-[120px]']) }}>
    <div class="mx-auto max-w-[1200px] px-5 md:px-8 lg:px-10">
        {{ $slot }}
    </div>
</section>