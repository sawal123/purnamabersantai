@props([
    'show' => false,
    'title' => 'Hapus data ini?',
    'description' => 'Data yang dihapus tidak dapat dikembalikan. Pastikan Anda memilih data yang benar.',
    'cancelAction' => null,
    'confirmAction' => null,
    'cancelLabel' => 'Batal',
    'confirmLabel' => 'Ya, Hapus',
])

@if ($show)
    <div class="fixed inset-0 z-[90]">
        @if ($cancelAction)
            <button type="button" class="absolute inset-0 bg-slate-950/70 dashboard-fade-in" wire:click="{{ $cancelAction }}" aria-label="Close modal"></button>
        @else
            <div class="absolute inset-0 bg-slate-950/70 dashboard-fade-in" aria-hidden="true"></div>
        @endif

        <div class="relative flex min-h-full items-center justify-center p-4">
            <div class="dashboard-scale-in w-full max-w-md rounded-3xl border border-slate-200 bg-white p-6 text-center shadow-xl dark:border-slate-700 dark:bg-slate-900">
                <div class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400">
                    <x-ui-dashboard.icon name="trash" class="h-8 w-8" />
                </div>

                <h2 class="mt-5 text-xl font-extrabold tracking-tight text-slate-950 dark:text-white">{{ $title }}</h2>
                <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">
                    {{ $description }}
                </p>

                @if ($slot->isNotEmpty())
                    <div class="mt-4 text-sm leading-6 text-slate-500 dark:text-slate-400">
                        {{ $slot }}
                    </div>
                @endif

                <div class="mt-6 grid grid-cols-2 gap-3">
                    @if ($cancelAction)
                        <x-ui-dashboard.button type="button" variant="ghost" wire:click="{{ $cancelAction }}">
                            {{ $cancelLabel }}
                        </x-ui-dashboard.button>
                    @else
                        <x-ui-dashboard.button type="button" variant="ghost">
                            {{ $cancelLabel }}
                        </x-ui-dashboard.button>
                    @endif

                    @if ($confirmAction)
                        <x-ui-dashboard.button type="button" variant="danger" wire:click="{{ $confirmAction }}" wire:loading.attr="disabled">
                            {{ $confirmLabel }}
                        </x-ui-dashboard.button>
                    @else
                        <x-ui-dashboard.button type="button" variant="danger" wire:loading.attr="disabled">
                            {{ $confirmLabel }}
                        </x-ui-dashboard.button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
