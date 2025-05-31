<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - InternshipTracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/lucide.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/lucide.min.js"></script>
    <style>
        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fade-in-right {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-fade-in-up {
            animation: fade-in-up 0.8s ease-out;
        }

        .animate-fade-in-right {
            animation: fade-in-right 0.8s ease-out 0.2s both;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-amber-50 via-orange-50 to-yellow-50">
    
    <!-- Navigation -->
    <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-amber-200">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center shadow-lg">
                        <i data-lucide="graduation-cap" class="w-6 h-6 text-white"></i>
                    </div>
                    <div class="flex flex-col">
                        <h1 class="text-xl font-bold text-gray-800">InternshipTracker</h1>
                        <p class="text-xs text-gray-600">Admin Dashboard</p>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <button onclick="scrollToSection('features')" class="text-gray-600 hover:text-amber-600 font-medium transition-colors">
                        Admin Features
                    </button>
                    <button onclick="scrollToSection('management')" class="text-gray-600 hover:text-amber-600 font-medium transition-colors">
                        Management Tools
                    </button>
                    <button onclick="scrollToSection('stats')" class="text-gray-600 hover:text-amber-600 font-medium transition-colors">
                        Statistics
                    </button>
                </div>

                <!-- Auth Buttons -->
                <div class="flex items-center space-x-3">
                    <a href="{{ url('/dashboard') }}" class="text-gray-600 hover:text-amber-600 font-medium transition-colors">
                        Dashboard
                    </a>
                    <a href="{{ url('/admin/login') }}" class="bg-gradient-to-r from-amber-400 to-orange-400 hover:from-amber-500 hover:to-orange-500 text-white px-4 py-2 rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                        Login
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative py-20 overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute inset-0 bg-gradient-to-r from-amber-400/10 to-orange-400/10"></div>
        <div class="absolute top-20 left-20 w-72 h-72 bg-amber-300/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-20 w-96 h-96 bg-orange-300/20 rounded-full blur-3xl"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <!-- Left Content -->
                <div class="space-y-8 animate-fade-in-up">
                    <div class="space-y-6">
                        <div class="bg-amber-100 text-amber-800 border border-amber-200 px-4 py-2 rounded-full inline-block">
                            👨‍💼 Administrative Control Panel
                        </div>

                        <h1 class="text-5xl lg:text-6xl font-bold text-gray-900 leading-tight">
                            Manage
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-orange-500">
                                Internship
                            </span>
                            Programs
                        </h1>

                        <p class="text-xl text-gray-600 leading-relaxed max-w-2xl">
                            Comprehensive administrative dashboard for managing students, teachers, industries, and internship programs. Monitor all activities from a single, powerful interface.
                        </p>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ url('/dashboard') }}" class="bg-gradient-to-r from-amber-400 to-orange-400 hover:from-amber-500 hover:to-orange-500 text-white px-8 py-4 text-lg font-semibold shadow-2xl hover:shadow-3xl transform hover:scale-105 transition-all duration-300 rounded-lg inline-flex items-center justify-center">
                            Access Dashboard
                            <i data-lucide="arrow-right" class="ml-2 w-5 h-5"></i>
                        </a>

                        <button onclick="scrollToSection('features')" class="border-2 border-amber-300 text-amber-700 hover:bg-amber-50 px-8 py-4 text-lg font-semibold rounded-lg inline-flex items-center justify-center">
                            <i data-lucide="play" class="mr-2 w-5 h-5"></i>
                            Learn More
                        </button>
                    </div>

                    <!-- Trust Indicators -->
                    <div class="flex items-center space-x-6 pt-8">
                        <div class="flex items-center space-x-2">
                            <div class="flex">
                                <i data-lucide="star" class="w-5 h-5 text-amber-400 fill-current"></i>
                                <i data-lucide="star" class="w-5 h-5 text-amber-400 fill-current"></i>
                                <i data-lucide="star" class="w-5 h-5 text-amber-400 fill-current"></i>
                                <i data-lucide="star" class="w-5 h-5 text-amber-400 fill-current"></i>
                                <i data-lucide="star" class="w-5 h-5 text-amber-400 fill-current"></i>
                            </div>
                            <span class="text-sm text-gray-600">Managing 50+ institutions</span>
                        </div>
                    </div>
                </div>

                <!-- Right Content - Admin Dashboard Preview -->
                <div class="relative animate-fade-in-right">
                    <div class="relative z-10">
                        <!-- Mock Admin Dashboard Card -->
                        <div class="transform rotate-2 hover:rotate-0 transition-transform duration-500 shadow-2xl border-0 bg-white/90 backdrop-blur rounded-lg p-6">
                            <div class="flex justify-between items-center mb-6">
                                <div>
                                    <h3 class="text-xl font-semibold">Admin Overview</h3>
                                    <p class="text-gray-600">System management panel</p>
                                </div>
                                <div class="bg-emerald-100 text-emerald-800 border border-emerald-200 px-3 py-1 rounded-full text-sm">
                                    Online
                                </div>
                            </div>

                            <!-- Quick Stats Grid -->
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-4 rounded-lg">
                                    <div class="flex items-center space-x-2">
                                        <i data-lucide="users" class="w-5 h-5 text-blue-600"></i>
                                        <span class="text-sm text-blue-600">Students</span>
                                    </div>
                                    <p class="text-2xl font-bold text-blue-700">1,247</p>
                                </div>
                                <div class="bg-gradient-to-r from-green-50 to-green-100 p-4 rounded-lg">
                                    <div class="flex items-center space-x-2">
                                        <i data-lucide="user-check" class="w-5 h-5 text-green-600"></i>
                                        <span class="text-sm text-green-600">Teachers</span>
                                    </div>
                                    <p class="text-2xl font-bold text-green-700">87</p>
                                </div>
                                <div class="bg-gradient-to-r from-purple-50 to-purple-100 p-4 rounded-lg">
                                    <div class="flex items-center space-x-2">
                                        <i data-lucide="building-2" class="w-5 h-5 text-purple-600"></i>
                                        <span class="text-sm text-purple-600">Industries</span>
                                    </div>
                                    <p class="text-2xl font-bold text-purple-700">156</p>
                                </div>
                                <div class="bg-gradient-to-r from-amber-50 to-amber-100 p-4 rounded-lg">
                                    <div class="flex items-center space-x-2">
                                        <i data-lucide="trending-up" class="w-5 h-5 text-amber-600"></i>
                                        <span class="text-sm text-amber-600">Active</span>
                                    </div>
                                    <p class="text-2xl font-bold text-amber-700">92%</p>
                                </div>
                            </div>

                            <!-- Recent Activity -->
                            <div class="space-y-3">
                                <h4 class="font-medium text-gray-700">Recent Activity</h4>
                                <div class="space-y-2">
                                    <div class="flex items-center space-x-3 text-sm">
                                        <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                                        <span class="text-gray-600">New student registered</span>
                                        <span class="text-gray-400">2m ago</span>
                                    </div>
                                    <div class="flex items-center space-x-3 text-sm">
                                        <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                                        <span class="text-gray-600">Industry partnership added</span>
                                        <span class="text-gray-400">15m ago</span>
                                    </div>
                                    <div class="flex items-center space-x-3 text-sm">
                                        <div class="w-2 h-2 bg-purple-400 rounded-full"></div>
                                        <span class="text-gray-600">Report generated</span>
                                        <span class="text-gray-400">1h ago</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Floating Elements -->
                        <div class="absolute -top-4 -right-4 w-20 h-20 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center shadow-xl animate-bounce">
                            <i data-lucide="shield-check" class="w-10 h-10 text-white"></i>
                        </div>

                        <div class="absolute -bottom-6 -left-6 w-16 h-16 bg-gradient-to-br from-blue-400 to-blue-500 rounded-full flex items-center justify-center shadow-lg animate-pulse">
                            <i data-lucide="settings" class="w-8 h-8 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <button onclick="scrollToSection('stats')" class="text-amber-600 hover:text-amber-700">
                <i data-lucide="chevron-down" class="w-8 h-8"></i>
            </button>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="stats" class="py-20 bg-white/50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    System Performance Overview
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Real-time metrics and statistics from your internship management system
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center group hover:transform hover:scale-105 transition-all duration-300">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-blue-400 to-blue-500 rounded-2xl flex items-center justify-center shadow-xl group-hover:shadow-2xl">
                        <i data-lucide="users" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-900 mb-2">1,247</h3>
                    <p class="text-gray-600 font-medium">Total Students</p>
                </div>

                <div class="text-center group hover:transform hover:scale-105 transition-all duration-300">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-green-400 to-green-500 rounded-2xl flex items-center justify-center shadow-xl group-hover:shadow-2xl">
                        <i data-lucide="building-2" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-900 mb-2">156</h3>
                    <p class="text-gray-600 font-medium">Partner Industries</p>
                </div>

                <div class="text-center group hover:transform hover:scale-105 transition-all duration-300">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-purple-400 to-purple-500 rounded-2xl flex items-center justify-center shadow-xl group-hover:shadow-2xl">
                        <i data-lucide="graduation-cap" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-900 mb-2">87</h3>
                    <p class="text-gray-600 font-medium">Supervising Teachers</p>
                </div>

                <div class="text-center group hover:transform hover:scale-105 transition-all duration-300">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl flex items-center justify-center shadow-xl group-hover:shadow-2xl">
                        <i data-lucide="trending-up" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-900 mb-2">95%</h3>
                    <p class="text-gray-600 font-medium">Success Rate</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Admin Features Section -->
    <section id="features" class="py-20">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <div class="bg-amber-100 text-amber-800 border border-amber-200 px-4 py-2 rounded-full inline-block mb-4">
                    ⚡ Admin Features
                </div>
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    Powerful Administrative Tools
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Complete control over your internship management system with advanced administrative capabilities
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="group">
                    <div class="h-full border-0 shadow-lg hover:shadow-2xl transition-all duration-300 bg-white/80 backdrop-blur group-hover:transform group-hover:scale-105 rounded-lg p-6">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                            <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">User Management</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Complete control over student, teacher, and industry partner accounts with role-based permissions and bulk operations.
                        </p>
                    </div>
                </div>

                <div class="group">
                    <div class="h-full border-0 shadow-lg hover:shadow-2xl transition-all duration-300 bg-white/80 backdrop-blur group-hover:transform group-hover:scale-105 rounded-lg p-6">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4">
                            <i data-lucide="bar-chart-3" class="w-6 h-6 text-green-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Analytics & Reports</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Comprehensive reporting system with real-time analytics, performance metrics, and exportable data visualizations.
                        </p>
                    </div>
                </div>

                <div class="group">
                    <div class="h-full border-0 shadow-lg hover:shadow-2xl transition-all duration-300 bg-white/80 backdrop-blur group-hover:transform group-hover:scale-105 rounded-lg p-6">
                        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-4">
                            <i data-lucide="settings" class="w-6 h-6 text-purple-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">System Configuration</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Advanced system settings, workflow customization, and platform configuration tools for optimal performance.
                        </p>
                    </div>
                </div>

                <div class="group">
                    <div class="h-full border-0 shadow-lg hover:shadow-2xl transition-all duration-300 bg-white/80 backdrop-blur group-hover:transform group-hover:scale-105 rounded-lg p-6">
                        <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-4">
                            <i data-lucide="shield-check" class="w-6 h-6 text-amber-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Security & Compliance</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Advanced security features, audit logs, data protection compliance, and automated backup systems.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Management Tools Section -->
    <section id="management" class="py-20 bg-gradient-to-br from-amber-50 to-orange-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <div class="bg-amber-100 text-amber-800 border border-amber-200 px-4 py-2 rounded-full inline-block mb-4">
                    🛠️ Management Tools
                </div>
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Streamlined Administration</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Efficient tools designed for seamless administrative workflow
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="relative text-center group">
                    <div class="relative z-10">
                        <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-blue-400 to-blue-500 rounded-full flex items-center justify-center shadow-xl group-hover:shadow-2xl group-hover:scale-110 transition-all duration-300">
                            <i data-lucide="user-plus" class="w-10 h-10 text-white"></i>
                        </div>

                        <div class="bg-white/80 backdrop-blur rounded-2xl p-6 shadow-lg group-hover:shadow-xl transition-all duration-300">
                            <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold mx-auto mb-4">
                                1
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">
                                Account Management
                            </h3>
                            <p class="text-gray-600 text-sm leading-relaxed">
                                Create, modify, and manage user accounts with comprehensive profile management and access control.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="relative text-center group">
                    <div class="relative z-10">
                        <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-green-400 to-green-500 rounded-full flex items-center justify-center shadow-xl group-hover:shadow-2xl group-hover:scale-110 transition-all duration-300">
                            <i data-lucide="monitor" class="w-10 h-10 text-white"></i>
                        </div>

                        <div class="bg-white/80 backdrop-blur rounded-2xl p-6 shadow-lg group-hover:shadow-xl transition-all duration-300">
                            <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold mx-auto mb-4">
                                2
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">
                                System Monitoring
                            </h3>
                            <p class="text-gray-600 text-sm leading-relaxed">
                                Real-time system monitoring with performance metrics, user activity tracking, and health checks.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="relative text-center group">
                    <div class="relative z-10">
                        <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-purple-400 to-purple-500 rounded-full flex items-center justify-center shadow-xl group-hover:shadow-2xl group-hover:scale-110 transition-all duration-300">
                            <i data-lucide="file-text" class="w-10 h-10 text-white"></i>
                        </div>

                        <div class="bg-white/80 backdrop-blur rounded-2xl p-6 shadow-lg group-hover:shadow-xl transition-all duration-300">
                            <div class="w-8 h-8 bg-purple-500 text-white rounded-full flex items-center justify-center text-sm font-bold mx-auto mb-4">
                                3
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">
                                Report Generation
                            </h3>
                            <p class="text-gray-600 text-sm leading-relaxed">
                                Generate comprehensive reports with customizable parameters and automated scheduling options.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="relative text-center group">
                    <div class="relative z-10">
                        <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center shadow-xl group-hover:shadow-2xl group-hover:scale-110 transition-all duration-300">
                            <i data-lucide="database" class="w-10 h-10 text-white"></i>
                        </div>

                        <div class="bg-white/80 backdrop-blur rounded-2xl p-6 shadow-lg group-hover:shadow-xl transition-all duration-300">
                            <div class="w-8 h-8 bg-amber-500 text-white rounded-full flex items-center justify-center text-sm font-bold mx-auto mb-4">
                                4
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">
                                Data Management
                            </h3>
                            <p class="text-gray-600 text-sm leading-relaxed">
                                Advanced data management with backup, restore, export capabilities and data integrity checks.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-amber-500 to-orange-500 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="absolute top-0 left-0 w-full h-full opacity-10">
            <div class="absolute top-20 left-20 w-32 h-32 border border-white/30 rounded-full"></div>
            <div class="absolute bottom-20 right-20 w-48 h-48 border border-white/20 rounded-full"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 border border-white/10 rounded-full"></div>
        </div>

        <div class="container mx-auto px-4 text-center relative z-10">
            <div class="max-w-4xl mx-auto space-y-8">
                <h2 class="text-4xl lg:text-5xl font-bold text-white leading-tight">
                    Ready to Take Control of Your System?
                </h2>
                <p class="text-xl text-amber-100 max-w-2xl mx-auto">
                    Access your administrative dashboard and start managing your internship programs with powerful, intuitive tools.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center pt-8">
                    <a href="{{ url('/dashboard') }}" class="bg-white text-amber-600 hover:bg-amber-50 px-8 py-4 text-lg font-semibold shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300 rounded-lg inline-flex items-center justify-center">
                        Access Dashboard
                        <i data-lucide="arrow-right" class="ml-2 w-5 h-5"></i>
                    </a>

                    <a href="{{ url('/admin/login') }}" class="border-2 border-white text-white hover:bg-white hover:text-amber-600 px-8 py-4 text-lg font-semibold transition-all duration-300 rounded-lg inline-flex items-center justify-center">
                        Admin Login
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Brand -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center">
                            <i data-lucide="graduation-cap" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg">InternshipTracker</h3>
                            <p class="text-sm text-gray-400">Admin Portal</p>
                        </div>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Comprehensive administrative solution for managing internship programs across educational institutions.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="font-semibold mb-4">Admin Tools</h4>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <a href="{{ url('/dashboard') }}" class="text-gray-400 hover:text-amber-400 transition-colors">
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/users') }}" class="text-gray-400 hover:text-amber-400 transition-colors">
                                User Management
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/reports') }}" class="text-gray-400 hover:text-amber-400 transition-colors">
                                Reports
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/settings') }}" class="text-gray-400 hover:text-amber-400 transition-colors">
                                System Settings
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Management -->
                <div>
                    <h4 class="font-semibold mb-4">Management</h4>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <a href="{{ url('/students') }}" class="text-gray-400 hover:text-amber-400 transition-colors">
                                Students
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/teachers') }}" class="text-gray-400 hover:text-amber-400 transition-colors">
                                Teachers
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/industries') }}" class="text-gray-400 hover:text-amber-400 transition-colors">
                                Industries
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/internships') }}" class="text-gray-400 hover:text-amber-400 transition-colors">
                                Internships
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h4 class="font-semibold mb-4">Support</h4>
                    <div class="space-y-2 text-sm text-gray-400">
                        <p>📧 admin@internshiptracker.com</p>
                        <p>📞 +62 123 456 7890</p>
                        <p>📍 Jakarta, Indonesia</p>
                        <p>🕐 24/7 Admin Support</p>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-12 pt-8 text-center">
                <p class="text-gray-400 text-sm">
                    © 2024 InternshipTracker Admin Portal. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Smooth scrolling function
        function scrollToSection(sectionId) {
            const element = document.getElementById(sectionId);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth' });
            }
        }

        // Add fade-in animation on load
        window.addEventListener('load', function() {
            setTimeout(function() {
                const elements = document.querySelectorAll('.animate-fade-in-up, .animate-fade-in-right');
                elements.forEach(function(element) {
                    element.style.opacity = '1';
                });
            }, 100);
        });

        // Add hover effects to cards
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.group');
            cards.forEach(function(card) {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.02)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        });
    </script>
</body>
</html>