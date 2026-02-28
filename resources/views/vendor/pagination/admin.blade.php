@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation"
        class="isolate inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-white p-1 shadow-sm">
        @if ($paginator->onFirstPage())
            <span class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg px-2 text-slate-300"
                aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M11.78 14.78a.75.75 0 0 1-1.06 0l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 1 1 1.06 1.06L8.06 10l3.72 3.72a.75.75 0 0 1 0 1.06Z"
                        clip-rule="evenodd" />
                </svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg px-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
                aria-label="{{ __('pagination.previous') }}">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M11.78 14.78a.75.75 0 0 1-1.06 0l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 1 1 1.06 1.06L8.06 10l3.72 3.72a.75.75 0 0 1 0 1.06Z"
                        clip-rule="evenodd" />
                </svg>
            </a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span
                    class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg px-2 text-xs font-semibold text-slate-400"
                    aria-disabled="true">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page"
                            class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg bg-indigo-600 px-3 text-xs font-bold text-white">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}"
                            class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg px-3 text-xs font-semibold text-slate-600 transition hover:bg-indigo-50 hover:text-indigo-700"
                            aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg px-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
                aria-label="{{ __('pagination.next') }}">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 1 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z"
                        clip-rule="evenodd" />
                </svg>
            </a>
        @else
            <span class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg px-2 text-slate-300"
                aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 1 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z"
                        clip-rule="evenodd" />
                </svg>
            </span>
        @endif
    </nav>
@endif
