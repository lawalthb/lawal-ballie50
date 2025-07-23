@extends('layouts.app')

@section('title', 'Ballie - Smart Accounting for African Businesses')
@section('description', 'Complete accounting, inventory, CRM, and payroll solution designed for Nigerian and African businesses. Start your free trial today.')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-primary-50 to-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 leading-tight mb-6">
                    Smart Accounting for
                    <span class="text-primary-600">African Businesseslawal</span>
                </h1>
                <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                    Manage your finances, inventory, customers, and payroll in one powerful platform.
                    Built specifically for Nigerian and African businesses with local compliance and currency support.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 mb-8">
                    <a href="{{ route('register') }}" class="bg-primary-500 text-white px-8 py-4 rounded-lg hover:bg-primary-600 font-semibold text-lg transition-colors text-center">
                        Start Free Trial
                    </a>
                    <a href="#demo" class="border-2 border-primary-500 text-primary-600 px-8 py-4 rounded-lg hover:bg-primary-50 font-semibold text-lg transition-colors text-center">
                        Watch Demo
                    </a>
                </div>
                <div class="flex items-center space-x-6 text-sm text-gray-500">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-accent-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        30-day free trial
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-accent-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        No credit card required
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-accent-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Local support
                    </div>
                </div>
            </div>
            <div class="relative">
                <div class="bg-white rounded-2xl shadow-2xl p-8 border border-gray-100">
                    <div class="bg-gray-100 rounded-lg h-64 flex items-center justify-center mb-4">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <p class="text-gray-500">Dashboard Preview</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Revenue</span>
                            <span class="font-semibold text-accent-600">₦2,450,000</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Outstanding Invoices</span>
                            <span class="font-semibold text-primary-600">₦850,000</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Inventory Value</span>
                            <span class="font-semibold text-gray-900">₦1,200,000</span>
                        </div>
                    </div>
                </div>
                <!-- Floating elements -->
                <div class="absolute -top-4 -right-4 bg-accent-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                    Live Data
                </div>
                <div class="absolute -bottom-4 -left-4 bg-primary-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                    Multi-Currency
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Overview -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Everything Your Business Needs
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                From accounting to payroll, inventory to CRM - manage every aspect of your business with one comprehensive solution.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Accounting -->
            <div class="bg-white border border-gray-200 rounded-xl p-8 hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Double-Entry Accounting</h3>
                <p class="text-gray-600 mb-4">Complete accounting system with automated double-entry bookkeeping, financial statements, and Nigerian tax compliance.</p>
                <ul class="text-sm text-gray-500 space-y-2">
                    <li class="flex items-center"><span class="w-1.5 h-1.5 bg-primary-500 rounded-full mr-2"></span>Chart of Accounts</li>
                    <li class="flex items-center"><span class="w-1.5 h-1.5 bg-primary-500 rounded-full mr-2"></span>Financial Reports</li>
                    <li class="flex items-center"><span class="w-1.5 h-1.5 bg-primary-500 rounded-full mr-2"></span>VAT Management</li>
                </ul>
            </div>

            <!-- Inventory -->
            <div class="bg-white border border-gray-200 rounded-xl p-8 hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 bg-accent-100 rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Inventory Management</h3>
                <p class="text-gray-600 mb-4">Track stock levels, manage suppliers, automate reordering, and get real-time inventory insights.</p>
                <ul class="text-sm text-gray-500 space-y-2">
                    <li class="flex items-center"><span class="w-1.5 h-1.5 bg-accent-500 rounded-full mr-2"></span>Stock Tracking</li>
                    <li class="flex items-center"><span class="w-1.5 h-1.5 bg-accent-500 rounded-full mr-2"></span>Supplier Management</li>
                    <li class="flex items-center"><span class="w-1.5 h-1.5 bg-accent-500 rounded-full mr-2"></span>Low Stock Alerts</li>
                </ul>
            </div>

            <!-- CRM -->
            <div class="bg-white border border-gray-200 rounded-xl p-8 hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Customer Management</h3>
                <p class="text-gray-600 mb-4">Build stronger relationships with comprehensive customer profiles, sales tracking, and communication history.</p>
                <ul class="text-sm text-gray-500 space-y-2">
                    <li class="flex items-center"><span class="w-1.5 h-1.5 bg-purple-500 rounded-full mr-2"></span>Customer Profiles</li>
                    <li class="flex items-center"><span class="w-1.5 h-1.5 bg-purple-500 rounded-full mr-2"></span>Sales Pipeline</li>
                    <li class="flex items-center"><span class="w-1.5 h-1.5 bg-purple-500 rounded-full mr-2"></span>Communication Log</li>
                </ul>
            </div>

            <!-- POS -->
            <div class="bg-white border border-gray-200 rounded-xl p-8 hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m-3-6h6M9 10.5h6"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Point of Sale</h3>
                <p class="text-gray-600 mb-4">Modern POS system for retail and service businesses with offline capability and mobile support.</p>
                <ul class="text-sm text-gray-500 space-y-2">
                    <li class="flex items-center"><span class="w-1.5 h-1.5 bg-orange-500 rounded-full mr-2"></span>Quick Checkout</li>
                    <li class="flex items-center"><span class="w-1.5 h-1.5 bg-orange-500 rounded-full mr-2"></span>Receipt Printing</li>
                    <li class="flex items-center"><span class="w-1.5 h-1.5 bg-orange-500 rounded-full mr-2"></span>Offline Mode</li>
                </ul>
            </div>

            <!-- Payroll -->
            <div class="bg-white border border-gray-200 rounded-xl p-8 hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Payroll Management</h3>
                <p class="text-gray-600 mb-4">Automated payroll processing with Nigerian tax calculations, pension contributions, and statutory deductions.</p>
                <ul class="text-sm text-gray-500 space-y-2">
                    <li class="flex items-center"><span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-2"></span>PAYE Calculation</li>
                    <li class="flex items-center"><span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-2"></span>Pension Integration</li>
                    <li class="flex items-center"><span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-2"></span>Payslip Generation</li>
                </ul>
            </div>

            <!-- Reporting -->
            <div class="bg-white border border-gray-200 rounded-xl p-8 hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Advanced Reporting</h3>
                <p class="text-gray-600 mb-4">Comprehensive reports and analytics to help you make informed business decisions with real-time insights.</p>
                <ul class="text-sm text-gray-500 space-y-2">
                    <li class="flex items-center"><span class="w-1.5 h-1.5 bg-indigo-500 rounded-full mr-2"></span>Financial Reports</li>
                    <li class="flex items-center"><span class="w-1.5 h-1.5 bg-indigo-500 rounded-full mr-2"></span>Custom Dashboards</li>
                    <li class="flex items-center"><span class="w-1.5 h-1.5 bg-indigo-500 rounded-full mr-2"></span>Export Options</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Ballie -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                    Built for African Businesses
                </h2>
                <p class="text-lg text-gray-600 mb-8">
                    Unlike generic accounting software, Ballie understands the unique challenges and requirements of businesses in Nigeria and across Africa.
                </p>

                <div class="space-y-6">
                    <div class="flex items-start space-x-4">
                        <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <svg class="w-4 h-4 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Local Compliance</h3>
                            <p class="text-gray-600">Built-in support for Nigerian VAT, PAYE, and other local tax requirements. Stay compliant effortlessly.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <svg class="w-4 h-4 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Multi-Currency Support</h3>
                            <p class="text-gray-600">Handle Naira, Dollars, and other currencies seamlessly. Perfect for import/export businesses.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <svg class="w-4 h-4 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Local Support</h3>
                            <p class="text-gray-600">Get help when you need it with local customer support that understands your business context.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <svg class="w-4 h-4 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Mobile Optimized</h3>
                            <p class="text-gray-600">Access your business data anywhere, anytime. Fully responsive design works on all devices.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative">
                <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                    <div class="bg-gradient-to-br from-primary-50 to-accent-50 rounded-lg p-6 mb-6">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                                <span class="text-2xl">🇳🇬</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Nigerian Business Ready</h4>
                            <p class="text-sm text-gray-600">Complete compliance and local features</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-700">VAT Compliance</span>
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Active</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-700">PAYE Calculation</span>
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Active</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-700">Naira Support</span>
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Active</span>
                        </div>
                    </div>
                </div>

                <!-- Floating badges -->
                <div class="absolute -top-4 -left-4 bg-accent-500 text-white px-4 py-2 rounded-full text-sm font-medium">
                    🏆 #1 Choice
                </div>
                <div class="absolute -bottom-4 -right-4 bg-primary-500 text-white px-4 py-2 rounded-full text-sm font-medium">
                    🚀 Fast Setup
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Trusted by Growing Businesses
            </h2>
            <p class="text-xl text-gray-600">
                See what business owners across Nigeria are saying about Ballie
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-sm">
                <div class="flex items-center mb-4">
                    <div class="flex text-yellow-400">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                    </div>
                </div>
                <p class="text-gray-600 mb-6 italic">
                    "Ballie transformed how we manage our retail business. The POS system is incredibly fast and the inventory tracking has saved us from stockouts. Best investment we've made!"
                </p>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center mr-4">
                        <span class="text-gray-600 font-semibold">AO</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Adebayo Ogundimu</p>
                        <p class="text-sm text-gray-500">CEO, Ogundimu Stores, Lagos</p>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-sm">
                <div class="flex items-center mb-4">
                    <div class="flex text-yellow-400">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                    </div>
                </div>
                <p class="text-gray-600 mb-6 italic">
                    "The payroll feature is a game-changer. PAYE calculations are automatic and accurate. Our accountant loves the detailed reports. Highly recommended for any Nigerian business."
                </p>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center mr-4">
                        <span class="text-gray-600 font-semibold">FN</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Fatima Nuhu</p>
                        <p class="text-sm text-gray-500">HR Manager, TechHub Abuja</p>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-sm">
                <div class="flex items-center mb-4">
                    <div class="flex text-yellow-400">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                    </div>
                </div>
                <p class="text-gray-600 mb-6 italic">
                    "Finally, an accounting software that understands Nigerian business! The VAT reports are perfect for FIRS submissions. Customer support is excellent too."
                </p>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center mr-4">
                        <span class="text-gray-600 font-semibold">CE</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Chidi Eze</p>
                        <p class="text-sm text-gray-500">Founder, Eze Logistics, Port Harcourt</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('cta')
@endsection
