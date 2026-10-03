@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@php
    $greeting = 'Good Evening';
    if (now()->hour < 12) {
        $greeting = 'Good Morning';
    } elseif (now()->hour < 17) {
        $greeting = 'Good Afternoon';
    }
@endphp

@section('content')
<div class="dashboard-page">

    {{-- ============================================= --}}
    {{-- HERO SECTION - Premium Gradient --}}
    {{-- ============================================= --}}
    <div class="hero-section mb-4">
        <div class="hero-content">
            <div class="hero-left">
                <div class="hero-greeting">
                    <span class="greeting-badge">
                        <i class="bi bi-stars"></i>
                        {{ $greeting }}
                    </span>
                    <h1 class="hero-title">
                        Welcome Back, <span class="text-gradient">{{ auth()->user()->name ?? 'Administrator' }}</span>
                    </h1>
                    <p class="hero-subtitle">
                        <i class="bi bi-arrow-right-circle"></i>
                        Manage students, payments, attendance, classes, and ID cards from one premium dashboard.
                    </p>
                </div>
                <div class="hero-stats-mini">
                    <div class="mini-stat">
                        <span class="mini-stat-value">{{ $studentsCount ?? 0 }}</span>
                        <span class="mini-stat-label">Students</span>
                    </div>
                    <div class="mini-stat-divider"></div>
                    <div class="mini-stat">
                        <span class="mini-stat-value">{{ $teachersCount ?? 0 }}</span>
                        <span class="mini-stat-label">Teachers</span>
                    </div>
                    <div class="mini-stat-divider"></div>
                    <div class="mini-stat">
                        <span class="mini-stat-value">{{ $classesCount ?? 0 }}</span>
                        <span class="mini-stat-label">Classes</span>
                    </div>
                </div>
            </div>
            <div class="hero-right">
                <div class="hero-date-time">
                    <div class="date-box">
                        <i class="bi bi-calendar-event"></i>
                        <span>{{ now()->format('d M Y') }}</span>
                    </div>
                    <div class="time-box">
                        <i class="bi bi-clock"></i>
                        <span id="liveTime">{{ now()->format('h:i A') }}</span>
                    </div>
                </div>
                <div class="hero-weather">
                    <i class="bi bi-cloud-sun"></i>
                    <span>Colombo, LK</span>
                </div>
            </div>
        </div>
        <div class="hero-decoration">
            <div class="decoration-circle c1"></div>
            <div class="decoration-circle c2"></div>
            <div class="decoration-circle c3"></div>
            <div class="decoration-circle c4"></div>
        </div>
    </div>

    {{-- ============================================= --}}
    {{-- ALERT - Low Stock Warning --}}
    {{-- ============================================= --}}
    @if ($showStudentCardWarning ?? false)
        <div class="alert-card warning mb-4">
            <div class="alert-icon">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <div class="alert-content">
                <strong>Warning!</strong> Student cards are running low.
                Remaining stock: <span class="alert-highlight">{{ $availableStudentCardCount ?? 0 }}</span>
                <span class="alert-badge">Low Stock</span>
            </div>
            <button type="button" class="alert-close" data-bs-dismiss="alert">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif

    {{-- ============================================= --}}
    {{-- STATS CARDS - Glass Morphism Style --}}
    {{-- ============================================= --}}
    <div class="stats-grid">
        {{-- Total Students --}}
        <div class="stat-card-glow stat-card-blue">
            <div class="stat-card-inner">
                <div class="stat-icon-wrapper">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Total Students</span>
                    <h2 class="stat-number">{{ $studentsCount ?? 0 }}</h2>
                    <span class="stat-trend up">
                        <i class="bi bi-arrow-up-short"></i>
                        +{{ rand(5, 15) }}% this month
                    </span>
                </div>
            </div>
            <div class="stat-progress">
                <div class="stat-progress-bar" style="width: 85%;"></div>
            </div>
        </div>

        {{-- Total Teachers --}}
        <div class="stat-card-glow stat-card-green">
            <div class="stat-card-inner">
                <div class="stat-icon-wrapper">
                    <i class="bi bi-person-workspace"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Total Teachers</span>
                    <h2 class="stat-number">{{ $teachersCount ?? 0 }}</h2>
                    <span class="stat-trend">
                        <i class="bi bi-check-circle-fill"></i>
                        Active faculty
                    </span>
                </div>
            </div>
            <div class="stat-progress">
                <div class="stat-progress-bar" style="width: 70%;"></div>
            </div>
        </div>

        {{-- Running Classes --}}
        <div class="stat-card-glow stat-card-orange">
            <div class="stat-card-inner">
                <div class="stat-icon-wrapper">
                    <i class="bi bi-building"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Running Classes</span>
                    <h2 class="stat-number">{{ $classesCount ?? 0 }}</h2>
                    <span class="stat-trend">
                        <i class="bi bi-calendar-check"></i>
                        Active courses
                    </span>
                </div>
            </div>
            <div class="stat-progress">
                <div class="stat-progress-bar" style="width: 60%;"></div>
            </div>
        </div>

        {{-- Today's Income --}}
        <div class="stat-card-glow stat-card-purple">
            <div class="stat-card-inner">
                <div class="stat-icon-wrapper">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Today's Income</span>
                    <h2 class="stat-number">Rs. {{ number_format($todayIncome ?? 0, 2) }}</h2>
                    <span class="stat-trend up">
                        <i class="bi bi-arrow-up-short"></i>
                        Updated now
                    </span>
                </div>
            </div>
            <div class="stat-progress">
                <div class="stat-progress-bar" style="width: 90%;"></div>
            </div>
        </div>
    </div>

    {{-- ============================================= --}}
    {{-- QUICK ACTIONS - Premium Cards --}}
    {{-- ============================================= --}}
    <div class="quick-actions-section mb-4">
        <div class="quick-actions-header">
            <div>
                <h5 class="section-title">
                    <i class="bi bi-lightning-fill text-warning"></i>
                    Quick Actions
                </h5>
                <p class="section-subtitle">Quickly access frequently used functions</p>
            </div>
            <span class="badge-actions">⚡ 5 Actions</span>
        </div>

        <div class="quick-actions-grid">
            @if (hasPermission('students.create'))
                <button type="button" class="quick-action-btn qa-primary" data-href="{{ route('admin.students.create') }}">
                    <div class="qa-icon"><i class="bi bi-person-plus-fill"></i></div>
                    <div class="qa-text">
                        <span class="qa-title">Add Student</span>
                        <span class="qa-desc">New enrollment</span>
                    </div>
                    <i class="bi bi-arrow-right qa-arrow"></i>
                </button>
            @endif

            @if (hasPermission('new-payment.index'))
                <button type="button" class="quick-action-btn qa-success" data-href="{{ route('admin.new-payment.index') }}">
                    <div class="qa-icon"><i class="bi bi-credit-card-2-front-fill"></i></div>
                    <div class="qa-text">
                        <span class="qa-title">Add Payment</span>
                        <span class="qa-desc">Record payment</span>
                    </div>
                    <i class="bi bi-arrow-right qa-arrow"></i>
                </button>
            @endif

            @if (hasPermission('student-classes.create'))
                <button type="button" class="quick-action-btn qa-warning" data-href="{{ route('admin.student-classes.create') }}">
                    <div class="qa-icon"><i class="bi bi-calendar-plus-fill"></i></div>
                    <div class="qa-text">
                        <span class="qa-title">Create Class</span>
                        <span class="qa-desc">New class setup</span>
                    </div>
                    <i class="bi bi-arrow-right qa-arrow"></i>
                </button>
            @endif

            @if (hasPermission('student-class-management.index'))
                <button type="button" class="quick-action-btn qa-info" data-href="{{ route('admin.student-class-management.index') }}">
                    <div class="qa-icon"><i class="bi bi-person-workspace"></i></div>
                    <div class="qa-text">
                        <span class="qa-title">Class Mgmt</span>
                        <span class="qa-desc">Manage enrollments</span>
                    </div>
                    <i class="bi bi-arrow-right qa-arrow"></i>
                </button>
            @endif

            @if (hasPermission('monthly-report.index'))
                <button type="button" class="quick-action-btn qa-dark" data-href="{{ route('admin.monthly-report.index') }}">
                    <div class="qa-icon"><i class="bi bi-file-earmark-bar-graph"></i></div>
                    <div class="qa-text">
                        <span class="qa-title">Reports</span>
                        <span class="qa-desc">View analytics</span>
                    </div>
                    <i class="bi bi-arrow-right qa-arrow"></i>
                </button>
            @endif
        </div>
    </div>

    {{-- ============================================= --}}
    {{-- CHART SECTION - Premium Chart Card --}}
    {{-- ============================================= --}}
    <div class="chart-premium-card mb-4">
        <div class="chart-premium-header">
            <div>
                <h5 class="chart-premium-title">
                    <i class="bi bi-graph-up-arrow text-primary"></i>
                    Institute Yearly Payment Report
                </h5>
                <p class="chart-premium-subtitle">Monthly payment analytics with dual dataset comparison</p>
            </div>
            <div class="chart-controls">
                <div class="chart-legend">
                    <span class="legend-item">
                        <span class="legend-dot blue"></span>
                        Total Payments
                    </span>
                    <span class="legend-item">
                        <span class="legend-dot green"></span>
                        Institute Income
                    </span>
                </div>
                <select id="yearSelector" class="year-selector-premium">
                    @for ($y = 2022; $y <= now()->year; $y++)
                        <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>
        </div>
        <div class="chart-premium-body">
            <div class="chart-container-premium">
                <canvas id="yearlyPaymentChart"></canvas>
                <div id="chartLoading" class="chart-loading-premium" style="display: none;">
                    <div class="spinner-premium"></div>
                </div>
            </div>
            <div class="chart-premium-stats">
                <div class="premium-stat-item">
                    <span class="premium-stat-label">Total Revenue</span>
                    <span class="premium-stat-value total" id="totalRevenue">Rs. 0.00</span>
                </div>
                <div class="premium-stat-divider"></div>
                <div class="premium-stat-item">
                    <span class="premium-stat-label">Institute Income</span>
                    <span class="premium-stat-value total" id="instituteIncome">Rs. 0.00</span>
                </div>
                <div class="premium-stat-divider"></div>
                <div class="premium-stat-item">
                    <span class="premium-stat-label">Best Month</span>
                    <span class="premium-stat-value" id="bestMonth">-</span>
                </div>
                <div class="premium-stat-divider"></div>
                <div class="premium-stat-item">
                    <span class="premium-stat-label">Annual Growth</span>
                    <span class="premium-stat-value" id="growthRate">0%</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================= --}}
    {{-- BOTTOM SECTION - Two Column Layout --}}
    {{-- ============================================= --}}
    <div class="row g-4">
        {{-- Student Cards Stock --}}
        <div class="col-xl-4">
            <div class="card-stock-premium">
                <div class="card-stock-header">
                    <div>
                        <span class="card-stock-label">Available Student Cards</span>
                        <h2 class="card-stock-number">{{ $availableStudentCardCount ?? 0 }}</h2>
                    </div>
                    <div class="card-stock-icon">
                        <i class="bi bi-person-vcard-fill"></i>
                    </div>
                </div>
                <div class="card-stock-status">
                    @if ($showStudentCardWarning ?? false)
                        <span class="status-badge warning">
                            <i class="bi bi-exclamation-triangle"></i> Low Stock
                        </span>
                    @else
                        <span class="status-badge success">
                            <i class="bi bi-check-circle"></i> Enough Stock
                        </span>
                    @endif
                </div>
                @if (($availableStudentCardCount ?? 0) < 20)
                    <div class="card-stock-progress">
                        <div class="stock-progress-bar">
                            <div class="stock-progress-fill" style="width: {{ min(100, (($availableStudentCardCount ?? 0) / 50) * 100) }}%;"></div>
                        </div>
                        <span class="stock-progress-label">Reorder when stock reaches 20</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Incomplete Registrations --}}
        <div class="col-xl-8">
            <div class="table-premium-card">
                <div class="table-premium-header">
                    <div>
                        <h5 class="table-premium-title">
                            <i class="bi bi-hourglass-split text-warning"></i>
                            Incomplete Registrations
                        </h5>
                        <span class="table-premium-badge">
                            <i class="bi bi-hourglass-split"></i>
                            {{ $incompleteRegistrationCount ?? 0 }} Pending
                        </span>
                    </div>
                </div>
                <div class="table-premium-body">
                    <div class="table-responsive">
                        <table class="table premium-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Guardian Mobile</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($incompleteRegistrations ?? [] as $student)
                                    <tr>
                                        <td><span class="id-badge">{{ $student->custom_id ?? '-' }}</span></td>
                                        <td>
                                            <div class="student-name-cell">
                                                <span class="avatar-xs">{{ strtoupper(substr($student->initial_name ?? 'N', 0, 1)) }}</span>
                                                {{ $student->initial_name ?? '-' }}
                                            </div>
                                        </td>
                                        <td>{{ $student->guardian_mobile ?? '-' }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.students.edit', $student->id) }}" 
                                               class="btn-complete">
                                                <i class="bi bi-check2-circle"></i> Complete
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center empty-table">
                                            <i class="bi bi-check-circle fs-2 d-block mb-2 text-success"></i>
                                            No incomplete registrations found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Latest Students - Full Width --}}
        <div class="col-12">
            <div class="table-premium-card">
                <div class="table-premium-header">
                    <div>
                        <h5 class="table-premium-title">
                            <i class="bi bi-clock-history text-info"></i>
                            Latest Students
                        </h5>
                        <span class="table-premium-badge primary">
                            <i class="bi bi-clock"></i> Recent
                        </span>
                    </div>
                </div>
                <div class="table-premium-body">
                    <div class="table-responsive">
                        <table class="table premium-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Joined</th>
                                    <th class="text-end">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestStudents ?? [] as $student)
                                    <tr>
                                        <td><span class="id-badge">{{ $student->custom_id ?? '-' }}</span></td>
                                        <td>
                                            <div class="student-name-cell">
                                                <span class="avatar-xs">{{ strtoupper(substr($student->initial_name ?? 'N', 0, 1)) }}</span>
                                                {{ $student->initial_name ?? '-' }}
                                            </div>
                                        </td>
                                        <td>{{ $student->guardian_mobile ?? '-' }}</td>
                                        <td>{{ optional($student->created_at)->format('d M Y') }}</td>
                                        <td class="text-end">
                                            <span class="status-dot active"></span> Active
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center empty-table">
                                            <i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>
                                            No students found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================= --}}
{{-- SCRIPTS --}}
{{-- ============================================= --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function() {
        // =============================================
        // NAVIGATION HANDLER
        // =============================================
        document.querySelectorAll('button[data-href]').forEach(btn => {
            btn.addEventListener('click', function() {
                const href = this.getAttribute('data-href');
                if (href && href !== '#') window.location.href = href;
            });
        });

        // =============================================
        // LIVE TIME UPDATE
        // =============================================
        function updateTime() {
            const now = new Date();
            const timeStr = now.toLocaleString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            });
            const el = document.getElementById('liveTime');
            if (el) el.textContent = timeStr;
        }
        updateTime();
        setInterval(updateTime, 30000);

        // =============================================
        // CHART LOGIC
        // =============================================
        let yearlyChart = null;

        function formatCurrency(amount) {
            return 'Rs. ' + parseFloat(amount).toLocaleString('en-LK', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function showLoading(show) {
            const loader = document.getElementById('chartLoading');
            if (loader) loader.style.display = show ? 'flex' : 'none';
        }

        function updateStats(totalData, instituteData, labels) {
            const totalRevenue = totalData.reduce((a, b) => a + b, 0);
            const instituteRevenue = instituteData.reduce((a, b) => a + b, 0);
            let maxTotal = 0,
                maxIndex = 0;
            for (let i = 0; i < totalData.length; i++) {
                if (totalData[i] > maxTotal) {
                    maxTotal = totalData[i];
                    maxIndex = i;
                }
            }
            const firstHalf = totalData.slice(0, 6).reduce((a, b) => a + b, 0);
            const secondHalf = totalData.slice(6, 12).reduce((a, b) => a + b, 0);
            const growthRate = firstHalf > 0 ? ((secondHalf - firstHalf) / firstHalf * 100).toFixed(1) : 0;

            const totalEl = document.getElementById('totalRevenue');
            const instituteEl = document.getElementById('instituteIncome');
            const bestEl = document.getElementById('bestMonth');
            const growthEl = document.getElementById('growthRate');

            if (totalEl) totalEl.innerHTML = formatCurrency(totalRevenue);
            if (instituteEl) instituteEl.innerHTML = formatCurrency(instituteRevenue);
            if (bestEl) bestEl.innerHTML = `${labels[maxIndex]} (${formatCurrency(maxTotal)})`;
            if (growthEl) {
                growthEl.innerHTML = (growthRate >= 0 ? '+' : '') + growthRate + '%';
                growthEl.style.color = growthRate >= 0 ? '#10b981' : '#ef4444';
            }
        }

        function loadYearlyReport(year) {
            showLoading(true);
            const url = `{{ route('admin.institute-yearly-report') }}?year=${year}`;
            fetch(url)
                .then(response => response.json())
                .then(result => {
                    if (!result.success) throw new Error(result.message);
                    const canvas = document.getElementById('yearlyPaymentChart');
                    if (!canvas) return;
                    const ctx = canvas.getContext('2d');
                    if (yearlyChart) yearlyChart.destroy();

                    const gradientTotal = ctx.createLinearGradient(0, 0, 0, 300);
                    gradientTotal.addColorStop(0, 'rgba(37, 99, 235, 0.4)');
                    gradientTotal.addColorStop(1, 'rgba(37, 99, 235, 0.02)');

                    const gradientInstitute = ctx.createLinearGradient(0, 0, 0, 300);
                    gradientInstitute.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
                    gradientInstitute.addColorStop(1, 'rgba(16, 185, 129, 0.02)');

                    yearlyChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: result.labels,
                            datasets: [{
                                label: 'Total Payments',
                                data: result.total_payments,
                                borderColor: '#2563eb',
                                backgroundColor: gradientTotal,
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4,
                                pointRadius: 5,
                                pointBackgroundColor: '#2563eb',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                            }, {
                                label: 'Institute Income',
                                data: result.institution_payments,
                                borderColor: '#10b981',
                                backgroundColor: gradientInstitute,
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4,
                                pointRadius: 5,
                                pointBackgroundColor: '#10b981',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                intersect: false,
                                mode: 'index'
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: '#0f172a',
                                    titleColor: '#f8fafc',
                                    bodyColor: '#f8fafc',
                                    padding: 12,
                                    cornerRadius: 12,
                                    callbacks: {
                                        label: (ctx) =>
                                            `${ctx.dataset.label}: Rs. ${ctx.raw.toLocaleString('en-LK', { minimumFractionDigits: 2 })}`
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(0,0,0,0.04)'
                                    },
                                    ticks: {
                                        callback: (val) => 'Rs. ' + val.toLocaleString()
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });
                    updateStats(result.total_payments, result.institution_payments, result.labels);
                    showLoading(false);
                })
                .catch(() => {
                    showLoading(false);
                });
        }

        const yearSelector = document.getElementById('yearSelector');
        if (yearSelector) {
            loadYearlyReport(yearSelector.value);
            yearSelector.addEventListener('change', () => loadYearlyReport(yearSelector.value));
        }
    })();
</script>
@endsection

@push('styles')
<style>
    {{-- ============================================= --}}
    {{-- PAGE CONTAINER --}}
    {{-- ============================================= --}}
    .dashboard-page {
        animation: fadeIn 0.5s ease;
        padding: 1.5rem;
        max-width: 1600px;
        margin: 0 auto;
        background: #f8fafc;
        min-height: 100vh;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 20px rgba(37, 99, 235, 0.1); }
        50% { box-shadow: 0 0 40px rgba(37, 99, 235, 0.2); }
    }

    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }

    {{-- ============================================= --}}
    {{-- HERO SECTION --}}
    {{-- ============================================= --}}
    .hero-section {
        position: relative;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 30%, #2563eb 100%);
        border-radius: 28px;
        padding: 2.5rem 3rem;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(37, 99, 235, 0.25);
    }

    .hero-content {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        position: relative;
        z-index: 2;
        flex-wrap: wrap;
        gap: 1.5rem;
    }

    .hero-left {
        flex: 1;
        min-width: 280px;
    }

    .hero-greeting {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .greeting-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        padding: 0.4rem 1rem;
        border-radius: 20px;
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.8rem;
        font-weight: 600;
        width: fit-content;
    }

    .hero-title {
        font-size: 2rem;
        font-weight: 800;
        color: #fff;
        margin: 0.5rem 0 0.25rem;
        line-height: 1.2;
    }

    .text-gradient {
        background: linear-gradient(135deg, #60a5fa, #34d399);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .hero-subtitle {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.95rem;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .hero-subtitle i {
        color: #34d399;
    }

    .hero-stats-mini {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-top: 1rem;
        padding: 1rem 1.5rem;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.06);
        width: fit-content;
    }

    .mini-stat {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .mini-stat-value {
        font-size: 1.2rem;
        font-weight: 700;
        color: #fff;
    }

    .mini-stat-label {
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.6);
        font-weight: 500;
    }

    .mini-stat-divider {
        width: 1px;
        height: 24px;
        background: rgba(255, 255, 255, 0.1);
    }

    .hero-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 0.75rem;
        flex-shrink: 0;
    }

    .hero-date-time {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(10px);
        padding: 0.5rem 1rem;
        border-radius: 14px;
        border: 1px solid rgba(255, 255, 255, 0.06);
    }

    .date-box, .time-box {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.85rem;
        font-weight: 500;
    }

    .date-box i, .time-box i {
        font-size: 1rem;
        opacity: 0.7;
    }

    .hero-weather {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.8rem;
        background: rgba(255, 255, 255, 0.05);
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
    }

    .hero-weather i {
        font-size: 1.1rem;
        color: #fbbf24;
    }

    {{-- ============================================= --}}
    {{-- HERO DECORATION --}}
    {{-- ============================================= --}}
    .hero-decoration {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        overflow: hidden;
        pointer-events: none;
        z-index: 1;
    }

    .decoration-circle {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.03);
    }

    .decoration-circle.c1 {
        width: 300px;
        height: 300px;
        top: -100px;
        right: -50px;
        background: rgba(96, 165, 250, 0.05);
    }

    .decoration-circle.c2 {
        width: 200px;
        height: 200px;
        bottom: -80px;
        left: -40px;
        background: rgba(52, 211, 153, 0.04);
    }

    .decoration-circle.c3 {
        width: 150px;
        height: 150px;
        top: 50%;
        right: 200px;
        background: rgba(251, 191, 36, 0.03);
    }

    .decoration-circle.c4 {
        width: 100px;
        height: 100px;
        bottom: 20px;
        right: 120px;
        background: rgba(248, 113, 113, 0.03);
    }

    {{-- ============================================= --}}
    {{-- ALERT CARD --}}
    {{-- ============================================= --}}
    .alert-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.5rem;
        border-radius: 16px;
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border: 1px solid #f59e0b;
        position: relative;
    }

    .alert-card.warning {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border-color: #f59e0b;
    }

    .alert-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: rgba(245, 158, 11, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #d97706;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .alert-content {
        flex: 1;
        font-size: 0.9rem;
        color: #92400e;
    }

    .alert-content strong {
        color: #78350f;
    }

    .alert-highlight {
        font-weight: 700;
        color: #d97706;
        padding: 0.1rem 0.5rem;
        background: rgba(217, 119, 6, 0.1);
        border-radius: 6px;
    }

    .alert-badge {
        display: inline-block;
        font-size: 0.6rem;
        font-weight: 700;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        background: #f59e0b;
        color: #fff;
        margin-left: 0.5rem;
    }

    .alert-close {
        background: none;
        border: none;
        color: #92400e;
        font-size: 1rem;
        padding: 0.25rem;
        cursor: pointer;
        opacity: 0.6;
        transition: opacity 0.2s;
    }

    .alert-close:hover {
        opacity: 1;
    }

    {{-- ============================================= --}}
    {{-- STATS GRID --}}
    {{-- ============================================= --}}
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }

    .stat-card-glow {
        background: #fff;
        border-radius: 20px;
        padding: 1.5rem 1.75rem;
        border: 1px solid #f1f5f9;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }

    .stat-card-glow:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(0,0,0,0.08);
        border-color: transparent;
    }

    .stat-card-glow::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
    }

    .stat-card-blue::before { background: linear-gradient(90deg, #2563eb, #60a5fa); }
    .stat-card-green::before { background: linear-gradient(90deg, #10b981, #34d399); }
    .stat-card-orange::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
    .stat-card-purple::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }

    .stat-card-inner {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        position: relative;
        z-index: 1;
    }

    .stat-icon-wrapper {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }

    .stat-card-blue .stat-icon-wrapper {
        background: #eff6ff;
        color: #2563eb;
    }
    .stat-card-green .stat-icon-wrapper {
        background: #ecfdf5;
        color: #10b981;
    }
    .stat-card-orange .stat-icon-wrapper {
        background: #fffbeb;
        color: #f59e0b;
    }
    .stat-card-purple .stat-icon-wrapper {
        background: #f5f3ff;
        color: #8b5cf6;
    }

    .stat-info {
        flex: 1;
        min-width: 0;
    }

    .stat-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: block;
    }

    .stat-number {
        font-size: 1.6rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0.1rem 0 0.2rem;
        line-height: 1.2;
    }

    .stat-trend {
        font-size: 0.7rem;
        font-weight: 600;
        color: #94a3b8;
        display: inline-flex;
        align-items: center;
        gap: 0.2rem;
    }

    .stat-trend.up {
        color: #10b981;
    }

    .stat-trend.down {
        color: #ef4444;
    }

    .stat-progress {
        margin-top: 1rem;
        height: 3px;
        background: #f1f5f9;
        border-radius: 10px;
        overflow: hidden;
    }

    .stat-progress-bar {
        height: 100%;
        border-radius: 10px;
        background: linear-gradient(90deg, #2563eb, #60a5fa);
        transition: width 1s ease;
    }

    .stat-card-green .stat-progress-bar {
        background: linear-gradient(90deg, #10b981, #34d399);
    }
    .stat-card-orange .stat-progress-bar {
        background: linear-gradient(90deg, #f59e0b, #fbbf24);
    }
    .stat-card-purple .stat-progress-bar {
        background: linear-gradient(90deg, #8b5cf6, #a78bfa);
    }

    {{-- ============================================= --}}
    {{-- QUICK ACTIONS --}}
    {{-- ============================================= --}}
    .quick-actions-section {
        background: #fff;
        border-radius: 24px;
        padding: 1.5rem 2rem;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }

    .quick-actions-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .section-title {
        font-weight: 700;
        margin: 0;
        color: #0f172a;
        font-size: 1.05rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-subtitle {
        font-size: 0.85rem;
        color: #94a3b8;
        margin: 0;
    }

    .badge-actions {
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        background: #f1f5f9;
        color: #475569;
    }

    .quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1rem;
    }

    .quick-action-btn {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.9rem 1.2rem;
        border: none;
        border-radius: 14px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-align: left;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        position: relative;
        overflow: hidden;
        min-height: 64px;
        width: 100%;
    }

    .quick-action-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        border-color: transparent;
    }

    .quick-action-btn .qa-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .quick-action-btn .qa-text {
        flex: 1;
        min-width: 0;
    }

    .quick-action-btn .qa-title {
        display: block;
        font-weight: 600;
        font-size: 0.85rem;
        color: #0f172a;
    }

    .quick-action-btn .qa-desc {
        display: block;
        font-size: 0.7rem;
        color: #94a3b8;
        margin-top: 0.05rem;
    }

    .quick-action-btn .qa-arrow {
        font-size: 1rem;
        color: #94a3b8;
        transition: all 0.3s ease;
        flex-shrink: 0;
    }

    .quick-action-btn:hover .qa-arrow {
        transform: translateX(4px);
        color: #2563eb;
    }

    .qa-primary .qa-icon { background: #eff6ff; color: #2563eb; }
    .qa-success .qa-icon { background: #ecfdf5; color: #10b981; }
    .qa-warning .qa-icon { background: #fffbeb; color: #f59e0b; }
    .qa-info .qa-icon { background: #ecfeff; color: #06b6d4; }
    .qa-dark .qa-icon { background: #f1f5f9; color: #475569; }

    .qa-primary:hover { background: #2563eb; border-color: #2563eb; }
    .qa-primary:hover .qa-title { color: #fff; }
    .qa-primary:hover .qa-desc { color: rgba(255,255,255,0.7); }
    .qa-primary:hover .qa-icon { background: rgba(255,255,255,0.2); color: #fff; }
    .qa-primary:hover .qa-arrow { color: #fff; }

    .qa-success:hover { background: #10b981; border-color: #10b981; }
    .qa-success:hover .qa-title { color: #fff; }
    .qa-success:hover .qa-desc { color: rgba(255,255,255,0.7); }
    .qa-success:hover .qa-icon { background: rgba(255,255,255,0.2); color: #fff; }
    .qa-success:hover .qa-arrow { color: #fff; }

    .qa-warning:hover { background: #f59e0b; border-color: #f59e0b; }
    .qa-warning:hover .qa-title { color: #fff; }
    .qa-warning:hover .qa-desc { color: rgba(255,255,255,0.7); }
    .qa-warning:hover .qa-icon { background: rgba(255,255,255,0.2); color: #fff; }
    .qa-warning:hover .qa-arrow { color: #fff; }

    .qa-info:hover { background: #06b6d4; border-color: #06b6d4; }
    .qa-info:hover .qa-title { color: #fff; }
    .qa-info:hover .qa-desc { color: rgba(255,255,255,0.7); }
    .qa-info:hover .qa-icon { background: rgba(255,255,255,0.2); color: #fff; }
    .qa-info:hover .qa-arrow { color: #fff; }

    .qa-dark:hover { background: #0f172a; border-color: #0f172a; }
    .qa-dark:hover .qa-title { color: #fff; }
    .qa-dark:hover .qa-desc { color: rgba(255,255,255,0.7); }
    .qa-dark:hover .qa-icon { background: rgba(255,255,255,0.15); color: #fff; }
    .qa-dark:hover .qa-arrow { color: #fff; }

    {{-- ============================================= --}}
    {{-- CHART PREMIUM CARD --}}
    {{-- ============================================= --}}
    .chart-premium-card {
        background: #fff;
        border-radius: 24px;
        border: 1px solid #f1f5f9;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }

    .chart-premium-header {
        padding: 1.25rem 1.75rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        background: #fafcfd;
    }

    .chart-premium-title {
        font-weight: 700;
        margin: 0;
        color: #0f172a;
        font-size: 1rem;
    }

    .chart-premium-title i {
        margin-right: 0.5rem;
    }

    .chart-premium-subtitle {
        font-size: 0.8rem;
        color: #94a3b8;
        margin: 0;
    }

    .chart-controls {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        flex-wrap: wrap;
    }

    .chart-legend {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.75rem;
        font-weight: 500;
        color: #475569;
    }

    .legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 4px;
    }

    .legend-dot.blue { background: #2563eb; }
    .legend-dot.green { background: #10b981; }

    .year-selector-premium {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.35rem 0.8rem;
        font-size: 0.8rem;
        font-weight: 500;
        color: #475569;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .year-selector-premium:hover {
        border-color: #2563eb;
        background: #eff6ff;
    }

    .year-selector-premium:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .chart-premium-body {
        padding: 1.5rem 1.75rem;
    }

    .chart-container-premium {
        position: relative;
        height: 300px;
    }

    .chart-container-premium canvas {
        width: 100% !important;
        height: 100% !important;
    }

    .chart-loading-premium {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        z-index: 10;
    }

    .spinner-premium {
        width: 40px;
        height: 40px;
        border: 3px solid #e2e8f0;
        border-top-color: #2563eb;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .chart-premium-stats {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        padding-top: 1.25rem;
        margin-top: 1.25rem;
        border-top: 1px solid #f1f5f9;
        flex-wrap: wrap;
    }

    .premium-stat-item {
        flex: 1;
        text-align: center;
        min-width: 100px;
    }

    .premium-stat-label {
        display: block;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #94a3b8;
        font-weight: 600;
    }

    .premium-stat-value {
        display: block;
        font-size: 1.1rem;
        font-weight: 700;
        color: #2563eb;
        margin-top: 0.15rem;
    }

    .premium-stat-value.total {
        font-size: 1.25rem;
        color: #0f172a;
    }

    .premium-stat-divider {
        width: 1px;
        background: #e2e8f0;
    }

    {{-- ============================================= --}}
    {{-- STOCK CARD --}}
    {{-- ============================================= --}}
    .card-stock-premium {
        background: #fff;
        border-radius: 24px;
        padding: 1.5rem 1.75rem;
        border: 1px solid #f1f5f9;
        height: 100%;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        transition: all 0.3s ease;
    }

    .card-stock-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.06);
    }

    .card-stock-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .card-stock-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .card-stock-number {
        font-size: 2.2rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0.2rem 0 0.5rem;
    }

    .card-stock-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        color: #475569;
    }

    .card-stock-status {
        margin: 0.5rem 0 0.75rem;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-badge.success {
        background: #ecfdf5;
        color: #10b981;
    }

    .status-badge.warning {
        background: #fffbeb;
        color: #f59e0b;
    }

    .card-stock-progress {
        margin-top: 1rem;
    }

    .stock-progress-bar {
        height: 6px;
        background: #f1f5f9;
        border-radius: 10px;
        overflow: hidden;
    }

    .stock-progress-fill {
        height: 100%;
        border-radius: 10px;
        background: linear-gradient(90deg, #f59e0b, #fbbf24);
        transition: width 1s ease;
    }

    .stock-progress-label {
        display: block;
        font-size: 0.7rem;
        color: #94a3b8;
        margin-top: 0.4rem;
    }

    {{-- ============================================= --}}
    {{-- TABLE PREMIUM CARD --}}
    {{-- ============================================= --}}
    .table-premium-card {
        background: #fff;
        border-radius: 24px;
        border: 1px solid #f1f5f9;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        height: 100%;
    }

    .table-premium-header {
        padding: 1.25rem 1.75rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
        background: #fafcfd;
    }

    .table-premium-title {
        font-weight: 700;
        margin: 0;
        color: #0f172a;
        font-size: 1rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .table-premium-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.7rem;
        font-weight: 600;
        padding: 0.25rem 0.7rem;
        border-radius: 20px;
        background: #fef3c7;
        color: #d97706;
    }

    .table-premium-badge.primary {
        background: #eff6ff;
        color: #2563eb;
    }

    .table-premium-body {
        padding: 0;
    }

    .premium-table {
        margin: 0;
        font-size: 0.85rem;
    }

    .premium-table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.8rem 1.25rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .premium-table tbody td {
        padding: 0.8rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .premium-table tbody tr:last-child td {
        border-bottom: none;
    }

    .premium-table tbody tr:hover {
        background: #f8fafc;
    }

    .id-badge {
        display: inline-block;
        font-weight: 600;
        color: #475569;
        font-size: 0.8rem;
        background: #f1f5f9;
        padding: 0.1rem 0.6rem;
        border-radius: 6px;
    }

    .student-name-cell {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .avatar-xs {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2563eb, #60a5fa);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.6rem;
        font-weight: 700;
        flex-shrink: 0;
    }

    .btn-complete {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        background: #2563eb;
        color: #fff;
        text-decoration: none;
        transition: all 0.2s ease;
        border: none;
    }

    .btn-complete:hover {
        background: #1d4ed8;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    .status-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 0.3rem;
    }

    .status-dot.active {
        background: #10b981;
    }

    .status-dot.inactive {
        background: #ef4444;
    }

    .empty-table {
        padding: 2.5rem 1rem !important;
        color: #94a3b8;
    }

    .empty-table i {
        font-size: 2.5rem;
        opacity: 0.5;
    }

    {{-- ============================================= --}}
    {{-- RESPONSIVE --}}
    {{-- ============================================= --}}
    @media (max-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .quick-actions-grid {
            grid-template-columns: repeat(3, 1fr);
        }

        .chart-premium-stats {
            flex-direction: row;
            flex-wrap: wrap;
        }

        .premium-stat-divider {
            display: none;
        }
    }

    @media (max-width: 992px) {
        .hero-section {
            padding: 2rem;
        }

        .hero-title {
            font-size: 1.6rem;
        }

        .hero-stats-mini {
            padding: 0.75rem 1rem;
        }

        .quick-actions-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .chart-controls {
            flex-direction: column;
            align-items: flex-start;
            width: 100%;
        }

        .chart-legend {
            flex-wrap: wrap;
        }
    }

    @media (max-width: 768px) {
        .dashboard-page {
            padding: 0.75rem;
        }

        .hero-section {
            padding: 1.5rem;
            border-radius: 20px;
        }

        .hero-content {
            flex-direction: column;
            align-items: stretch;
        }

        .hero-right {
            align-items: flex-start;
        }

        .hero-title {
            font-size: 1.3rem;
        }

        .hero-stats-mini {
            width: 100%;
            justify-content: space-around;
        }

        .stats-grid {
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }

        .stat-card-glow {
            padding: 1rem 1.25rem;
        }

        .stat-number {
            font-size: 1.2rem;
        }

        .stat-icon-wrapper {
            width: 44px;
            height: 44px;
            font-size: 1.1rem;
        }

        .quick-actions-section {
            padding: 1rem 1.25rem;
            border-radius: 18px;
        }

        .quick-actions-grid {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }

        .quick-action-btn {
            min-height: 52px;
            padding: 0.7rem 1rem;
        }

        .chart-premium-header {
            flex-direction: column;
            align-items: flex-start;
            padding: 1rem 1.25rem;
        }

        .chart-premium-body {
            padding: 1rem 1.25rem;
        }

        .chart-container-premium {
            height: 200px;
        }

        .chart-premium-stats {
            flex-direction: column;
            gap: 0.5rem;
        }

        .premium-stat-item {
            text-align: left;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.3rem 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .premium-stat-item:last-child {
            border-bottom: none;
        }

        .premium-stat-value {
            font-size: 1rem;
        }

        .card-stock-premium {
            padding: 1rem 1.25rem;
            border-radius: 18px;
        }

        .card-stock-number {
            font-size: 1.8rem;
        }

        .table-premium-header {
            padding: 0.75rem 1rem;
        }

        .table-premium-body .premium-table {
            font-size: 0.75rem;
        }

        .premium-table thead th,
        .premium-table tbody td {
            padding: 0.5rem 0.6rem;
        }

        .avatar-xs {
            width: 24px;
            height: 24px;
            font-size: 0.5rem;
        }

        .btn-complete {
            font-size: 0.6rem;
            padding: 0.2rem 0.6rem;
        }
    }

    @media (max-width: 480px) {
        .hero-section {
            padding: 1rem;
            border-radius: 16px;
        }

        .hero-title {
            font-size: 1.1rem;
        }

        .hero-subtitle {
            font-size: 0.8rem;
        }

        .hero-stats-mini {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.3rem;
            padding: 0.5rem 0.75rem;
        }

        .mini-stat-divider {
            display: none;
        }

        .stats-grid {
            grid-template-columns: 1fr;
            gap: 0.6rem;
        }

        .stat-card-glow {
            padding: 0.8rem 1rem;
            border-radius: 16px;
        }

        .stat-number {
            font-size: 1.1rem;
        }

        .stat-icon-wrapper {
            width: 38px;
            height: 38px;
            font-size: 0.9rem;
            border-radius: 10px;
        }

        .quick-actions-section {
            padding: 0.75rem 1rem;
            border-radius: 14px;
        }

        .quick-action-btn {
            min-height: 44px;
            padding: 0.5rem 0.8rem;
        }

        .quick-action-btn .qa-icon {
            width: 32px;
            height: 32px;
            font-size: 0.85rem;
        }

        .quick-action-btn .qa-title {
            font-size: 0.75rem;
        }

        .quick-action-btn .qa-desc {
            font-size: 0.6rem;
        }

        .chart-premium-card {
            border-radius: 16px;
        }

        .chart-container-premium {
            height: 160px;
        }

        .card-stock-premium {
            padding: 0.8rem 1rem;
            border-radius: 14px;
        }

        .card-stock-number {
            font-size: 1.5rem;
        }

        .table-premium-card {
            border-radius: 14px;
        }

        .premium-table {
            font-size: 0.65rem;
        }

        .premium-table thead th,
        .premium-table tbody td {
            padding: 0.3rem 0.4rem;
        }

        .id-badge {
            font-size: 0.6rem;
            padding: 0.05rem 0.4rem;
        }

        .btn-complete {
            font-size: 0.5rem;
            padding: 0.15rem 0.4rem;
        }
    }
</style>
@endpush