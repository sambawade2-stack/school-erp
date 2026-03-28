@props(['href', 'label' => 'Retour', 'breadcrumb' => null])

<div class="flex items-center gap-2 mb-5">
    <a href="{{ $href }}"
       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        {{ $label }}
    </a>
    @if($breadcrumb)
    <span class="text-gray-300">/</span>
    <span class="text-sm text-gray-500">{{ $breadcrumb }}</span>
    @endif
</div>
