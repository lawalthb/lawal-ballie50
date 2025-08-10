<!-- More Actions Expandable Section -->
<div x-show="moreActionsExpanded"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-1 transform scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-1 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-95"
     class="bg-white rounded-2xl p-6 shadow-lg border border-gray-200">

    <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-bold text-gray-900">Quick Actions</h3>
        <div class="text-sm text-gray-500">Choose an action to get started</div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        <!-- Employee Management -->
        <a href="{{ route('tenant.payroll.employees.create', ['tenant' => $tenant->slug]) }}"
           class="modal-action-card p-6 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl text-white hover:from-blue-600 hover:to-blue-700 transition-all duration-300 group cursor-pointer">
            <div class="flex flex-col items-center text-center">
                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center mb-4 group-hover:bg-white/30 transition-colors duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <h4 class="font-semibold text-sm mb-2">Add Employee</h4>
                <p class="text-xs opacity-80">Create new employee record</p>
            </div>
        </a>

        <a href="{{ route('tenant.payroll.departments.index', ['tenant' => $tenant->slug]) }}"
           class="modal-action-card p-6 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl text-white hover:from-emerald-600 hover:to-emerald-700 transition-all duration-300 group cursor-pointer">
            <div class="flex flex-col items-center text-center">
                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center mb-4 group-hover:bg-white/30 transition-colors duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <h4 class="font-semibold text-sm mb-2">Manage Departments</h4>
                <p class="text-xs opacity-80">View departments</p>
            </div>
        </a>

        <a href="{{ route('tenant.payroll.components.index', ['tenant' => $tenant->slug]) }}"
           class="modal-action-card p-6 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl text-white hover:from-purple-600 hover:to-purple-700 transition-all duration-300 group cursor-pointer">
            <div class="flex flex-col items-center text-center">
                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center mb-4 group-hover:bg-white/30 transition-colors duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h4 class="font-semibold text-sm mb-2">Salary Components</h4>
                <p class="text-xs opacity-80">Manage components</p>
            </div>
        </a>

        <!-- Payroll Processing -->
        <a href="{{ route('tenant.payroll.processing.create', ['tenant' => $tenant->slug]) }}"
           class="modal-action-card p-6 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl text-white hover:from-indigo-600 hover:to-indigo-700 transition-all duration-300 group cursor-pointer">
            <div class="flex flex-col items-center text-center">
                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center mb-4 group-hover:bg-white/30 transition-colors duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <h4 class="font-semibold text-sm mb-2">Process Payroll</h4>
                <p class="text-xs opacity-80">Run payroll processing</p>
            </div>
        </a>

        <a href="{{ route('tenant.payroll.processing.index', ['tenant' => $tenant->slug]) }}"
           class="modal-action-card p-6 bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl text-white hover:from-teal-600 hover:to-teal-700 transition-all duration-300 group cursor-pointer">
            <div class="flex flex-col items-center text-center">
                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center mb-4 group-hover:bg-white/30 transition-colors duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
                <h4 class="font-semibold text-sm mb-2">Payroll History</h4>
                <p class="text-xs opacity-80">View past payrolls</p>
            </div>
        </a>

        <a href="{{ route('tenant.payroll.reports.tax-report', ['tenant' => $tenant->slug]) }}"
           class="modal-action-card p-6 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl text-white hover:from-orange-600 hover:to-orange-700 transition-all duration-300 group cursor-pointer">
            <div class="flex flex-col items-center text-center">
                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center mb-4 group-hover:bg-white/30 transition-colors duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h4 class="font-semibold text-sm mb-2">Tax Reports</h4>
                <p class="text-xs opacity-80">Generate tax reports</p>
            </div>
        </a>

        <!-- Management Actions -->
        <a href="{{ route('tenant.payroll.employees.index', ['tenant' => $tenant->slug]) }}"
           class="modal-action-card p-6 bg-gradient-to-br from-rose-500 to-rose-600 rounded-xl text-white hover:from-rose-600 hover:to-rose-700 transition-all duration-300 group cursor-pointer">
            <div class="flex flex-col items-center text-center">
                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center mb-4 group-hover:bg-white/30 transition-colors duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h4 class="font-semibold text-sm mb-2">All Employees</h4>
                <p class="text-xs opacity-80">Manage employees</p>
            </div>
        </a>
    </div>
</div>
