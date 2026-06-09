<div>
    <h1 class="text-2xl font-bold text-slate-900">Contact Queries</h1>
    <p class="mt-1 text-slate-600">Messages submitted from the public contact form.</p>

    <div class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Message</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($queries as $query)
                    <tr class="{{ $query->is_read ? '' : 'bg-sky-50/50' }}">
                        <td class="px-4 py-3 whitespace-nowrap text-slate-500">
                            {{ optional($query->created_at)->format('d M Y, H:i') ?? '—' }}
                        </td>
                        <td class="px-4 py-3 font-semibold">{{ $query->name }}</td>
                        <td class="px-4 py-3"><a href="mailto:{{ $query->email }}" class="text-sky-700 hover:underline">{{ $query->email }}</a></td>
                        <td class="max-w-xs px-4 py-3 text-slate-600">{{ \Illuminate\Support\Str::limit($query->message, 120) }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-1 text-xs font-bold {{ $query->is_read ? 'bg-slate-100 text-slate-600' : 'bg-sky-100 text-sky-700' }}">
                                {{ $query->is_read ? 'Read' : 'New' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-2">
                                @if ($query->is_read)
                                    <button wire:click="markUnread({{ $query->id }})" class="text-xs font-semibold text-slate-600 hover:underline">Mark unread</button>
                                @else
                                    <button wire:click="markRead({{ $query->id }})" class="text-xs font-semibold text-sky-700 hover:underline">Mark read</button>
                                @endif
                                <button wire:click="deleteQuery({{ $query->id }})" wire:confirm="Delete this query?" class="text-xs font-semibold text-red-600 hover:underline">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500">No contact queries yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $queries->links() }}
    </div>
</div>
