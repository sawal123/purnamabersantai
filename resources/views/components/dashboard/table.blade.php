@props([
    'columns' => [],
])

<div class="overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-950/60">
                <tr>
                    @foreach ($columns as $column)
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">
                            {{ $column['label'] }}
                        </th>
                    @endforeach
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">
                        Actions
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                {{ $slot }}
            </tbody>
        </table>
    </div>

    @isset($pagination)
        <div class="border-t border-zinc-200 px-4 py-4 dark:border-zinc-700">
            {{ $pagination }}
        </div>
    @endisset
</div>
