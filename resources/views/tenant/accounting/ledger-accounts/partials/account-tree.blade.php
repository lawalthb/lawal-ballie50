<div class="p-6">
    @forelse($accounts as $account)
        <div class="account-item border-b border-gray-100 last:border-b-0" data-account-id="{{ $account->id }}">
            <div class="py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center flex-1">
                        @if($account->hasChildren())
                            <button class="mr-3 p-1 rounded hover:bg-gray-100 toggle-children" type="button">
                                <svg class="w-4 h-4 text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        @else
                            <div class="w-6 h-6 mr-3"></div>
                        @endif

                        <div class="flex items-center space-x-4 flex-1">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $account->code }}
                                    </span>
                                    <h3 class="text-sm font-medium text-gray-900">{{ $account->name }}</h3>
                                    @if(!$account->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Inactive
                                        </span>
                                    @endif
                                </div>
                                @if($account->description)
                                    <p class="mt-1 text-xs text-gray-500">{{ Str::limit($account->description, 60) }}</p>
                                @endif
                            </div>

                            <div class="flex items-center space-x-6 text-sm">
                                <div class="text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $account->account_type === 'asset' ? 'bg-green-100 text-green-800' :
                                           ($account->account_type === 'liability' ? 'bg-red-100 text-red-800' :
                                           ($account->account_type === 'equity' ? 'bg-yellow-100 text-yellow-800' :
                                           ($account->account_type === 'income' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'))) }}">
                                        {{ ucfirst($account->account_type) }}
                                    </span>
                                </div>

                                <div class="text-center min-w-0">
                                    <p class="text-xs text-gray-500">Group</p>
                                    <p class="text-xs font-medium text-gray-900 truncate">{{ $account->accountGroup->name ?? 'N/A' }}</p>
                                </div>

                                <div class="text-right min-w-0">
                                    @php
                                        $balance = $account->getCurrentBalance();
                                        $balanceClass = $balance > 0 ? 'text-green-600' : ($balance < 0 ? 'text-red-600' : 'text-gray-500');
                                    @endphp
                                    <p class="text-xs text-gray-500">Balance</p>
                                    <p class="text-sm font-medium {{ $balanceClass }}">
                                        {{ number_format(abs($balance), 2) }}
                                        <span class="text-xs">{{ $balance >= 0 ? 'Dr' : 'Cr' }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 ml-4">
                        <a href="{{ route('tenant.accounting.ledger-accounts.show', [$tenant, $account]) }}"
                           class="inline-flex items-center p-1.5 border border-transparent text-xs font-medium rounded text-primary-700 bg-primary-100 hover:bg-primary-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </a>
                        <a href="{{ route('tenant.accounting.ledger-accounts.edit', [$tenant, $account]) }}"
                           class="inline-flex items-center p-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                        <button type="button"
                                onclick="confirmDelete('{{ $account->id }}', '{{ $account->name }}')"
                                class="inline-flex items-center p-1.5 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Children Accounts -->
                @if($account->children->count() > 0)
                    <div class="children-accounts ml-8 mt-4 space-y-3" style="display: none;">
                        @foreach($account->children as $child)
                            <div class="border-l-2 border-gray-200 pl-4">
                                <div class="flex items-center justify-between py-2">
                                    <div class="flex items-center space-x-4 flex-1">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ $child->code }}
                                                </span>
                                                <h4 class="text-sm font-medium text-gray-900">{{ $child->name }}</h4>
                                                @if(!$child->is_active)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        Inactive
                                                    </span>
                                                @endif
                                            </div>
                                            @if($child->description)
                                                <p class="mt-1 text-xs text-gray-500">{{ Str::limit($child->description, 60) }}</p>
                                            @endif
                                        </div>

                                        <div class="flex items-center space-x-6 text-sm">
                                            <div class="text-center">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    {{ $child->account_type === 'asset' ? 'bg-green-100 text-green-800' :
                                                       ($child->account_type === 'liability' ? 'bg-red-100 text-red-800' :
                                                       ($child->account_type === 'equity' ? 'bg-yellow-100 text-yellow-800' :
                                                       ($child->account_type === 'income' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'))) }}">
                                                    {{ ucfirst($child->account_type) }}
                                                </span>
                                            </div>

                                            <div class="text-center min-w-0">
                                                <p class="text-xs text-gray-500">Group</p>
                                                <p class="text-xs font-medium text-gray-900 truncate">{{ $child->accountGroup->name ?? 'N/A' }}</p>
                                            </div>

                                            <div class="text-right min-w-0">
                                                @php
                                                    $childBalance = $child->getCurrentBalance();
                                                    $childBalanceClass = $childBalance > 0 ? 'text-green-600' : ($childBalance < 0 ? 'text-red-600' : 'text-gray-500');
                                                @endphp
                                                <p class="text-xs text-gray-500">Balance</p>
                                                <p class="text-sm font-medium {{ $childBalanceClass }}">
                                                    {{ number_format(abs($childBalance), 2) }}
                                                    <span class="text-xs">{{ $childBalance >= 0 ? 'Dr' : 'Cr' }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-2 ml-4">
                                        <a href="{{ route('tenant.accounting.ledger-accounts.show', [$tenant, $child]) }}"
                                           class="inline-flex items-center p-1.5 border border-transparent text-xs font-medium rounded text-primary-700 bg-primary-100 hover:bg-primary-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('tenant.accounting.ledger-accounts.edit', [$tenant, $child]) }}"
                                           class="inline-flex items-center p-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <button type="button"
                                                onclick="confirmDelete('{{ $child->id }}', '{{ $child->name }}')"
                                                class="inline-flex items-center p-1.5 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No ledger accounts found</h3>
            <p class="mt-1 text-sm text-gray-500">
                @if(request()->hasAny(['search', 'account_type', 'account_group_id', 'is_active']))
                    No accounts match your current filters.
                @else
                    Get started by creating your first ledger account.
                @endif
            </p>
            <div class="mt-6">
                @if(request()->hasAny(['search', 'account_type', 'account_group_id', 'is_active']))
                    <a href="{{ route('tenant.accounting.ledger-accounts.index', ['tenant' => $tenant->slug, 'view' => 'tree']) }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700">
                        Clear Filters
                    </a>
                @else
                    <a href="{{ route('tenant.accounting.ledger-accounts.create', $tenant) }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create First Account
                    </a>
                @endif
            </div>
        </div>
    @endforelse
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle children visibility
    document.querySelectorAll('.toggle-children').forEach(function(button) {
        button.addEventListener('click', function() {
            const accountItem = this.closest('.account-item');
            const childrenDiv = accountItem.querySelector('.children-accounts');
            const icon = this.querySelector('svg');

            if (childrenDiv.style.display === 'none') {
                childrenDiv.style.display = 'block';
                icon.style.transform = 'rotate(90deg)';
            } else {
                childrenDiv.style.display = 'none';
                icon.style.transform = 'rotate(0deg)';
            }
        });
    });
});

function confirmDelete(accountId, accountName) {
    if (confirm(`Are you sure you want to delete the account "${accountName}"?`)) {
        // Create and submit delete form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('tenant.accounting.ledger-accounts.index', $tenant) }}/${accountId}`;

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';

        const tokenField = document.createElement('input');
        tokenField.type = 'hidden';
        tokenField.name = '_token';
        tokenField.value = '{{ csrf_token() }}';

        form.appendChild(methodField);
        form.appendChild(tokenField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
