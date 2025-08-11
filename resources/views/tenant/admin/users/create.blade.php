@extends('layouts.tenant')

@section('title', 'Create User')

@section('content')
    {{-- Page Header --}}
    @include('tenant.admin.partials.header', [
        'title' => 'Create New User',
        'subtitle' => 'Add a new user to your organization with appropriate roles and permissions.',
        'breadcrumb' => 'Users',
        'actions' => view('tenant.admin.partials.back-button', ['route' => route('tenant.admin.users.index', tenant('slug'))])
    ])

    <div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        {{-- Success/Error Messages --}}
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- User Creation Form --}}
        @component('tenant.admin.partials.form', [
            'action' => route('tenant.admin.users.store', tenant('slug')),
            'method' => 'POST',
            'title' => 'User Information',
            'subtitle' => 'Fill in the details below to create a new user account.'
        ])
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                {{-- Personal Information Section --}}
                <div class="sm:col-span-2">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h4>
                </div>

                {{-- First Name --}}
                @include('tenant.admin.partials.input-field', [
                    'name' => 'first_name',
                    'label' => 'First Name',
                    'placeholder' => 'Enter first name',
                    'required' => true
                ])

                {{-- Last Name --}}
                @include('tenant.admin.partials.input-field', [
                    'name' => 'last_name',
                    'label' => 'Last Name',
                    'placeholder' => 'Enter last name',
                    'required' => true
                ])

                {{-- Email --}}
                <div class="sm:col-span-2">
                    @include('tenant.admin.partials.input-field', [
                        'name' => 'email',
                        'type' => 'email',
                        'label' => 'Email Address',
                        'placeholder' => 'Enter email address',
                        'required' => true,
                        'help' => 'This will be used for login and notifications.'
                    ])
                </div>

                {{-- Account Settings Section --}}
                <div class="sm:col-span-2 mt-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Account Settings</h4>
                </div>

                {{-- Password --}}
                @include('tenant.admin.partials.input-field', [
                    'name' => 'password',
                    'type' => 'password',
                    'label' => 'Password',
                    'placeholder' => 'Enter password',
                    'required' => true,
                    'help' => 'Minimum 8 characters required.'
                ])

                {{-- Confirm Password --}}
                @include('tenant.admin.partials.input-field', [
                    'name' => 'password_confirmation',
                    'type' => 'password',
                    'label' => 'Confirm Password',
                    'placeholder' => 'Confirm password',
                    'required' => true
                ])

                {{-- Role Selection --}}
                <div class="sm:col-span-2">
                    @php
                        $roleOptions = [];
                        if(isset($roles)) {
                            foreach($roles as $role) {
                                $roleOptions[$role->id] = $role->name;
                            }
                        }
                    @endphp
                    @include('tenant.admin.partials.select-field', [
                        'name' => 'role_id',
                        'label' => 'Role',
                        'options' => $roleOptions,
                        'placeholder' => 'Select a role',
                        'required' => true,
                        'help' => 'Choose the role that determines user permissions.'
                    ])
                </div>

                {{-- Status --}}
                <div class="sm:col-span-2">
                    @include('tenant.admin.partials.select-field', [
                        'name' => 'status',
                        'label' => 'Account Status',
                        'options' => [
                            'active' => 'Active',
                            'inactive' => 'Inactive',
                            'pending' => 'Pending Activation'
                        ],
                        'value' => 'active',
                        'required' => true
                    ])
                </div>

                {{-- Additional Options --}}
                <div class="sm:col-span-2 mt-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Additional Options</h4>

                    <div class="space-y-4">
                        {{-- Send Welcome Email --}}
                        <div class="flex items-center">
                            <input id="send_welcome_email" name="send_welcome_email" type="checkbox" value="1" checked
                                   class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                            <label for="send_welcome_email" class="ml-2 block text-sm text-gray-900">
                                Send welcome email with login instructions
                            </label>
                        </div>

                        {{-- Force Password Change --}}
                        <div class="flex items-center">
                            <input id="force_password_change" name="force_password_change" type="checkbox" value="1"
                                   class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                            <label for="force_password_change" class="ml-2 block text-sm text-gray-900">
                                Force password change on first login
                            </label>
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
                    Create User
                </button>
            @endslot
        @endcomponent
    </div>
@endsection

@push('scripts')
<script>
    // Form validation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const passwordField = document.querySelector('[name="password"]');
        const confirmPasswordField = document.querySelector('[name="password_confirmation"]');

        // Password confirmation validation
        confirmPasswordField.addEventListener('blur', function() {
            if (this.value && this.value !== passwordField.value) {
                showFieldError('password_confirmation', 'Passwords do not match.');
            } else {
                clearFieldError('password_confirmation');
            }
        });

        // Email validation
        const emailField = document.querySelector('[name="email"]');
        emailField.addEventListener('blur', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && !emailRegex.test(this.value)) {
                showFieldError('email', 'Please enter a valid email address.');
            } else {
                clearFieldError('email');
            }
        });
    });
</script>
@endpush
