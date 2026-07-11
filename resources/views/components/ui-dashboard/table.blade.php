@props([
    'columns' => [],
])

<div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-soft dark:border-slate-800 dark:bg-slate-900">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
            <thead class="bg-slate-50 dark:bg-slate-800/60">
                <tr>
                    @foreach ($columns as $column)
                        <th class="px-4 py-3 text-left text-xs font-extrabold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">
                            {{ $column['label'] }}
                        </th>
                    @endforeach
                    <th class="px-4 py-3 text-right text-xs font-extrabold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">
                        Actions
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                {{ $slot }}
            </tbody>
        </table>
    </div>

    @isset($pagination)
        <div class="border-t border-slate-200 px-4 py-4 dark:border-slate-800">
            {{ $pagination }}
        </div>
    @endisset
</div>
