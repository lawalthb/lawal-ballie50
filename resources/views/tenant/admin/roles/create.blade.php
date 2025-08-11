@extends('layouts.tenant')

@section('title', 'Create Role')

@section('content')
    {{-- Page Header --}}
    @include('tenant.admin.partials.header', [
        'title' => 'Create New Role',
        'subtitle' => 'Define a new role with specific permissions for your organization.',
        'breadcrumb' => 'Roles',
        'actions' => view('tenant.admin.partials.back-button', ['route' => route('tenant.admin.roles.index', tenant('slug'))])
    ])

    <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        {{-- Role Creation Form --}}
        @component('tenant.admin.partials.form', [
            'action' => route('tenant.admin.roles.store', tenant('slug')),
            'method' => 'POST',
            'title' => 'Role Information',
            'subtitle' => 'Configure the role details and assign permissions.'
        ])
            <div class="grid grid-cols-1 gap-6">
                {{-- Basic Information --}}
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    {{-- Role Name --}}
                    @include('tenant.admin.partials.input-field', [
                        'name' => 'name',
                        'label' => 'Role Name',
                        'placeholder' => 'Enter role name (e.g., Manager)',
                        'required' => true,
                        'help' => 'A unique name for this role.'
                    ])

                    {{-- Display Name --}}
                    @include('tenant.admin.partials.input-field', [
                        'name' => 'display_name',
                        'label' => 'Display Name',
                        'placeholder' => 'Enter display name (e.g., Account Manager)',
                        'help' => 'Human-readable name shown in the interface.'
                    ])
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">
                        Description
                    </label>
                    <div class="mt-1">
                        <textarea id="description" name="description" rows="3"
                                  class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-purple-500 focus:border-purple-500"
                                  placeholder="Describe what this role can do...">{{ old('description') }}</textarea>
                    </div>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500">Provide a clear description of this role's responsibilities.</p>
                </div>

                {{-- Permissions Section --}}
                <div class="mt-8">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Permissions</h4>
                    <p class="text-sm text-gray-500 mb-6">Select the permissions that users with this role should have.</p>

                    {{-- Permission Groups --}}
                    @if(isset($permissions) && count($permissions) > 0)
                        <div class="space-y-6">
                            @foreach($permissions->groupBy('module') as $module => $modulePermissions)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <h5 class="text-sm font-medium text-gray-900 capitalize">
                                            {{ str_replace('_', ' ', $module) }} Module
                                        </h5>
                                        <button type="button" onclick="toggleModulePermissions('{{ $module }}')"
                                                class="text-sm text-purple-600 hover:text-purple-500">
                                            Select All
                                        </button>
                                    </div>

                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                        @foreach($modulePermissions as $permission)
                                            <div class="flex items-center">
                                                <input id="permission_{{ $permission->id }}"
                                                       name="permissions[]"
                                                       type="checkbox"
                                                       value="{{ $permission->id }}"
                                                       data-module="{{ $module }}"
                                                       class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                                <label for="permission_{{ $permission->id }}" class="ml-2 block text-sm text-gray-900">
                                                    {{ $permission->display_name ?? $permission->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6 bg-gray-50 rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.414-5.414l-4-4L8 3l4 4 5.414-5.414a2 2 0 012.828 2.828L15.828 9l4 4-1.414 1.414L14 10.414l-4 4-1.414-1.414L13 8.586 8.586 4.172a2 2 0 00-2.828 2.828z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No permissions available</h3>
                            <p class="mt-1 text-sm text-gray-500">Please create some permissions first.</p>
                        </div>
                    @endif
                </div>

                {{-- Role Settings --}}
                <div class="mt-8">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Role Settings</h4>

                    <div class="space-y-4">
                        {{-- Is Default Role --}}
                        <div class="flex items-center">
                            <input id="is_default" name="is_default" type="checkbox" value="1"
                                   class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                            <label for="is_default" class="ml-2 block text-sm text-gray-900">
                                Default role for new users
                            </label>
                        </div>

                        {{-- Is Admin Role --}}
                        <div class="flex items-center">
                            <input id="is_admin" name="is_admin" type="checkbox" value="1"
                                   class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                            <label for="is_admin" class="ml-2 block text-sm text-gray-900">
                                Administrator role (has all permissions)
                            </label>
                        </div>

                        {{-- Role Status --}}
                        <div>
                            @include('tenant.admin.partials.select-field', [
                                'name' => 'status',
                                'label' => 'Status',
                                'options' => [
                                    'active' => 'Active',
                                    'inactive' => 'Inactive'
                                ],
                                'value' => 'active',
                                'required' => true
                            ])
                        </div>
                    </div>
                </div>
            </div>

            @slot('actions')
                <button type="button" onclick="window.history.back()"
                        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    Cancel
                </button>
                <button type="submit"
                        class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Create Role
                </button>
            @endslot
        @endcomponent
    </div>
@endsection

@push('scripts')
<script>
    function toggleModulePermissions(module) {
        const checkboxes = document.querySelectorAll(`input[data-module="${module}"]`);
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);

        checkboxes.forEach(checkbox => {
            checkbox.checked = !allChecked;
        });
    }

    // Form validation
    document.addEventListener('DOMContentLoaded', function() {
        const nameField = document.querySelector('[name="name"]');

        // Auto-generate display name from name
        nameField.addEventListener('input', function() {
            const displayNameField = document.querySelector('[name="display_name"]');
            if (!displayNameField.value) {
                displayNameField.value = this.value.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            }
        });

        // Admin role checkbox logic
        const isAdminCheckbox = document.querySelector('[name="is_admin"]');
        const permissionCheckboxes = document.querySelectorAll('[name="permissions[]"]');

        isAdminCheckbox.addEventListener('change', function() {
            if (this.checked) {
                // Check all permissions
                permissionCheckboxes.forEach(cb => cb.checked = true);
                // Disable permission checkboxes
                permissionCheckboxes.forEach(cb => cb.disabled = true);
            } else {
                // Enable permission checkboxes
                permissionCheckboxes.forEach(cb => cb.disabled = false);
            }
        });
    });
</script>
@endpush
