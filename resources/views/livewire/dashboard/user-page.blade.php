<section class="w-full space-y-6">
    <div class="overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-indigo-700 to-violet-600 p-6 text-white shadow-xl shadow-indigo-500/15 sm:p-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-bold ring-1 ring-white/20">Access</span>
                <h1 class="mt-4 text-3xl font-extrabold tracking-tight sm:text-4xl">Users</h1>
                <p class="mt-3 text-sm leading-6 text-indigo-100 md:text-base">
                    Kelola akun yang bisa masuk ke dashboard Purnama Panel.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.2em] text-indigo-100">Total</p>
                    <p class="mt-2 text-2xl font-extrabold text-white">{{ $summary['total'] }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.2em] text-indigo-100">Verified</p>
                    <p class="mt-2 text-2xl font-extrabold text-white">{{ $summary['verified'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div class="grid gap-4 md:grid-cols-2 xl:w-[30rem]">
            <x-ui-dashboard.text-input label="Search" name="search" placeholder="Cari user..." error="search"
                wire:model.live.debounce.300ms="search" />

            <label class="block">
                <span class="mb-2 block text-sm font-bold text-slate-800 dark:text-slate-100">Rows per page</span>
                <select name="per_page" wire:model.live="perPage"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-950 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:ring-indigo-500/10">
                    @foreach ($this->perPageChoices as $option)
                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                    @endforeach
                </select>

                @error('perPage')
                    <span class="mt-2 block text-sm font-semibold text-rose-500">{{ $message }}</span>
                @enderror
            </label>
        </div>

        <x-ui-dashboard.button wire:click="create">
            Tambah User
        </x-ui-dashboard.button>
    </div>

    <x-ui-dashboard.table :columns="[
        ['label' => 'User'],
        ['label' => 'Email'],
        ['label' => 'Verified'],
        ['label' => 'Created'],
    ]">
        @forelse ($users as $user)
            <tr wire:key="dashboard-user-{{ $user->getKey() }}" class="align-top">
                <td class="px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">
                    <div class="flex items-center gap-3">
                        <span class="grid size-11 shrink-0 place-items-center rounded-2xl bg-indigo-600 text-sm font-extrabold uppercase text-white">
                            {{ $user->initials() }}
                        </span>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-900 dark:text-white">{{ $user->name }}</p>
                            @if ($user->getKey() === auth()->id())
                                <p class="mt-0.5 text-xs text-indigo-500 dark:text-indigo-300">Current user</p>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">{{ $user->email }}</td>
                <td class="px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">
                    <span class="{{ $user->email_verified_at ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-zinc-200 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' }} inline-flex rounded-full px-3 py-1 text-xs font-semibold">
                        {{ $user->email_verified_at ? 'Yes' : 'No' }}
                    </span>
                </td>
                <td class="px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">
                    {{ $user->created_at?->format('d M Y') ?? '-' }}
                </td>
                <td class="px-4 py-4">
                    <div class="flex justify-end gap-2">
                        <x-ui-dashboard.button size="sm" variant="ghost" wire:click="edit({{ $user->getKey() }})">
                            Edit
                        </x-ui-dashboard.button>
                        <x-ui-dashboard.button size="sm" variant="danger" wire:click="confirmDelete({{ $user->getKey() }})"
                            @disabled($user->getKey() === auth()->id())>
                            Delete
                        </x-ui-dashboard.button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-4 py-12 text-center text-sm text-zinc-500 dark:text-zinc-400">
                    Belum ada user.
                </td>
            </tr>
        @endforelse

        <x-slot:pagination>
            @if ($users->hasPages())
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                        Menampilkan {{ $users->firstItem() }}-{{ $users->lastItem() }} dari {{ $users->total() }} user
                    </p>

                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button"
                            class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-45 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                            wire:click="previousPage" wire:loading.attr="disabled" @disabled($users->onFirstPage())>
                            Prev
                        </button>
                        <button type="button"
                            class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-45 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                            wire:click="nextPage" wire:loading.attr="disabled" @disabled(! $users->hasMorePages())>
                            Next
                        </button>
                    </div>
                </div>
            @else
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                    Menampilkan {{ $users->count() }} user
                </p>
            @endif
        </x-slot:pagination>
    </x-ui-dashboard.table>

    <x-ui-dashboard.modal :show="$showFormModal" :title="$editingId ? 'Edit User' : 'Tambah User'"
        description="Password wajib saat membuat user baru, dan opsional saat edit." closeAction="closeFormModal" maxWidth="max-w-3xl"
        wire:key="dashboard-user-form-modal">
        <form wire:submit="save" class="grid gap-4 md:grid-cols-2">
            <x-ui-dashboard.text-input label="Name" name="name" error="form.name" wire:model="form.name" />
            <x-ui-dashboard.text-input label="Email" name="email" type="email" error="form.email" wire:model="form.email" />

            <x-ui-dashboard.text-input label="Password" name="password" type="password" error="form.password"
                wire:model="form.password" autocomplete="new-password" />
            <x-ui-dashboard.text-input label="Confirm Password" name="password_confirmation" type="password"
                error="form.password_confirmation" wire:model="form.password_confirmation" autocomplete="new-password" />

            <div class="md:col-span-2">
                <x-ui-dashboard.checkbox label="Email Verified" error="form.email_verified" wire:model="form.email_verified" />
            </div>

            <div class="md:col-span-2 flex justify-end gap-3 pt-2">
                <x-ui-dashboard.button variant="ghost" wire:click="closeFormModal">
                    Cancel
                </x-ui-dashboard.button>
                <x-ui-dashboard.button type="submit" wire:loading.attr="disabled">
                    Save Data
                </x-ui-dashboard.button>
            </div>
        </form>
    </x-ui-dashboard.modal>

    <x-ui-dashboard.confirm-modal
        :show="$showDeleteModal"
        title="Hapus user ini?"
        description="User yang dihapus tidak bisa login lagi ke dashboard."
        cancelAction="closeDeleteModal"
        confirmAction="delete"
        cancelLabel="Batal"
        confirmLabel="Ya, Hapus"
    />
</section>
