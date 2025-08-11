@extends('layouts.tenant')

@section('title', 'Create User - Debug')

@section('content')
    <div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold mb-6">Create New User (Debug Version)</h1>

        {{-- Simple Form without components --}}
        <div class="bg-white shadow sm:rounded-lg">
            <form method="POST" action="{{ route('tenant.admin.users.store', tenant('slug')) }}">
                @csrf

                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">User Information</h3>

                    <div class="grid grid-cols-1 gap-6">
                        {{-- First Name --}}
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="first_name" id="first_name" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="Enter first name">
                        </div>

                        {{-- Last Name --}}
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700">
                                Last Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="last_name" id="last_name" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="Enter last name">
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="Enter email address">
                        </div>

                        {{-- Password --}}
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password" id="password" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="Enter password">
                        </div>

                        {{-- Role --}}
                        <div>
                            <label for="role_id" class="block text-sm font-medium text-gray-700">
                                Role <span class="text-red-500">*</span>
                            </label>
                            <select name="role_id" id="role_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-purple-500 focus:border-purple-500">
                                <option value="">Select a role</option>
                                @if(isset($roles))
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 rounded-b-lg">
                    <button type="button" onclick="window.history.back()"
                            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        Cancel
                    </button>
                    <button type="submit"
                            class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        Create User
                    </button>
                </div>
            </form>
        </div>

        {{-- Debug Information --}}
        <div class="mt-6 bg-gray-100 p-4 rounded">
            <h4 class="font-medium mb-2">Debug Information:</h4>
            <p>Tenant: {{ tenant('slug') ?? 'Not set' }}</p>
            <p>Roles count: {{ isset($roles) ? count($roles) : 'Not set' }}</p>
            <p>Current route: {{ request()->route()->getName() }}</p>
        </div>
    </div>
@endsection
