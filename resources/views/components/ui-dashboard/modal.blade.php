@props([
    'show' => false,
    'title' => '',
    'description' => null,
    'closeAction' => null,
    'maxWidth' => 'max-w-4xl',
])

@if ($show)
    <div class="fixed inset-0 z-[80]">
        @if ($closeAction)
            <button type="button" class="absolute inset-0 bg-slate-950/70 dashboard-fade-in" wire:click="{{ $closeAction }}" aria-label="Close modal"></button>
        @else
            <div class="absolute inset-0 bg-slate-950/70 dashboard-fade-in" aria-hidden="true"></div>
        @endif

        <div class="relative flex min-h-full items-center justify-center p-4">
            <div class="dashboard-scale-in w-full {{ $maxWidth }} overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-900">
                <div class="flex items-start justify-between gap-4 border-b border-slate-200 p-5 dark:border-slate-800">
                    <div>
                        <h2 class="text-xl font-extrabold tracking-tight text-slate-950 dark:text-white">{{ $title }}</h2>
                        @if ($description)
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $description }}</p>
                        @endif
                    </div>

                    @if ($closeAction)
                        <button type="button" wire:click="{{ $closeAction }}" class="rounded-xl p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-white">
                            <span class="sr-only">Close</span>
                            <x-ui-dashboard.icon name="x" class="h-5 w-5" />
                        </button>
                    @endif
                </div>

                <div class="max-h-[75vh] overflow-y-auto p-5">
                    {{ $slot }}
                </div>

                @isset($footer)
                    <div class="flex flex-wrap justify-end gap-3 border-t border-slate-200 p-5 dark:border-slate-800">
                        {{ $footer }}
                    </div>
                @endisset
            </div>
        </div>
    </div>
@endif
