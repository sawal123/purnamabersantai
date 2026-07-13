@props([
    'show' => false,
    'title' => '',
    'description' => null,
    'closeAction' => null,
    'maxWidth' => 'max-w-4xl',
])

@if ($show)
    <div {{ $attributes->merge(['class' => 'fixed inset-0 z-[80] overflow-y-auto']) }}>
        @if ($closeAction)
            <button type="button" class="fixed inset-0 bg-slate-950/70 dashboard-fade-in" wire:click="{{ $closeAction }}" aria-label="Close modal"></button>
        @else
            <div class="fixed inset-0 bg-slate-950/70 dashboard-fade-in" aria-hidden="true"></div>
        @endif

        <div class="relative flex min-h-full items-start justify-center p-4 sm:p-6">
            <div class="dashboard-scale-in my-auto flex max-h-[calc(100vh-2rem)] w-full {{ $maxWidth }} flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-900 sm:max-h-[calc(100vh-3rem)]">
                <div class="shrink-0 flex items-start justify-between gap-4 border-b border-slate-200 p-5 dark:border-slate-800">
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

                <div class="min-h-0 flex-1 overflow-y-auto p-5">
                    {{ $slot }}
                </div>

                @isset($footer)
                    <div class="shrink-0 flex flex-wrap justify-end gap-3 border-t border-slate-200 p-5 dark:border-slate-800">
                        {{ $footer }}
                    </div>
                @endisset
            </div>
        </div>
    </div>
@endif
