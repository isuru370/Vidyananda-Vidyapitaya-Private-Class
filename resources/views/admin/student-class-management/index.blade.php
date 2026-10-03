@extends('layouts.app')

@section('title', 'Student Class Management')
@section('page-title', 'Student Class Management')

@section('content')

<div class="student-class-mgmt-page">

    {{-- ============================================= --}}
    {{-- STATS CARDS --}}
    {{-- ============================================= --}}
    <div class="row g-4 mb-4">

        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon blue">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <h3>{{ App\Models\Student::count() }}</h3>
                    <p>Total Students</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon green">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <h3>{{ App\Models\StudentClassEnrollment::where('is_active', true)->count() }}</h3>
                    <p>Active Enrollments</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon orange">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
                <div>
                    <h3>{{ App\Models\StudentClassEnrollment::where('is_active', false)->count() }}</h3>
                    <p>Inactive Enrollments</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon purple">
                    <i class="bi bi-journal-bookmark-fill"></i>
                </div>
                <div>
                    <h3>{{ App\Models\StudentClass::where('is_active', true)->count() }}</h3>
                    <p>Active Classes</p>
                </div>
            </div>
        </div>

    </div>

    {{-- ============================================= --}}
    {{-- MAIN CARD --}}
    {{-- ============================================= --}}
    <div class="main-card">

        {{-- HEADER --}}
        <div class="main-card-header">

            <div>
                <h4>Student Class Management</h4>
                <p>Search students by name, mobile number or custom ID and manage their class enrollments</p>
            </div>

        </div>

        {{-- ============================================= --}}
        {{-- SEARCH SECTION - UPDATED --}}
        {{-- ============================================= --}}
        <div class="search-card">

            <form method="POST" 
                  action="{{ route('admin.student-class-management.search') }}">
                @csrf

                <div class="row g-3">

                    <div class="col-lg-10">
                        <div class="search-input-wrapper">
                            <i class="bi bi-search"></i>
                            <input 
                                type="text" 
                                name="search" 
                                class="form-control custom-input @error('search') is-invalid @enderror"
                                placeholder="Search Student Name, Mobile Number or Custom ID"
                                value="{{ old('search') }}"
                                required
                            >
                            @error('search')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-lg-2">
                        <button 
                            type="submit" 
                            class="btn btn-primary w-100 custom-btn">
                            <i class="bi bi-search"></i>
                            Search
                        </button>
                    </div>

                </div>

                <div class="mt-3">
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i>
                        Search using Student Name, Mobile Number, WhatsApp Number or Custom ID.
                    </small>
                </div>

            </form>

        </div>

        {{-- ============================================= --}}
        {{-- QUICK ACTIONS --}}
        {{-- ============================================= --}}
        <div class="row g-4 mt-2">
            <div class="col-md-4">
                <a href="{{ route('admin.student-class-management.assign-form') }}" class="quick-action-card">
                    <div class="quick-action-icon blue">
                        <i class="bi bi-person-plus"></i>
                    </div>
                    <div>
                        <h6>Assign Class</h6>
                        <small>Enroll student in a class</small>
                    </div>
                    <i class="bi bi-chevron-right ms-auto"></i>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('admin.students.index') }}" class="quick-action-card">
                    <div class="quick-action-icon green">
                        <i class="bi bi-list-ul"></i>
                    </div>
                    <div>
                        <h6>All Students</h6>
                        <small>View all students</small>
                    </div>
                    <i class="bi bi-chevron-right ms-auto"></i>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('admin.student-classes.index') }}" class="quick-action-card">
                    <div class="quick-action-icon purple">
                        <i class="bi bi-journal-bookmark-fill"></i>
                    </div>
                    <div>
                        <h6>All Classes</h6>
                        <small>View all classes</small>
                    </div>
                    <i class="bi bi-chevron-right ms-auto"></i>
                </a>
            </div>
        </div>

    </div>
</div>

@endsection

@push('styles')
<style>
    .student-class-mgmt-page {
        animation: fadeIn .4s ease;
    }

    {{-- ============================================= --}}
    {{-- STATS CARDS --}}
    {{-- ============================================= --}}
    .stats-card {
        background: #fff;
        border-radius: 24px;
        padding: 1.5rem;
        display: flex;
        gap: 1rem;
        align-items: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .04);
        border: 1px solid #eef2f7;
        transition: all 0.3s ease;
    }

    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, .08);
    }

    .stats-icon {
        width: 60px;
        height: 60px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .blue { background: linear-gradient(135deg, #2563eb, #3b82f6); }
    .green { background: linear-gradient(135deg, #10b981, #34d399); }
    .orange { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
    .red { background: linear-gradient(135deg, #ef4444, #f87171); }
    .purple { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }

    .stats-card h3 {
        margin: 0;
        font-size: 1.6rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .stats-card p {
        margin: 0;
        color: #64748b;
        font-size: 0.9rem;
    }

    {{-- ============================================= --}}
    {{-- MAIN CARD --}}
    {{-- ============================================= --}}
    .main-card {
        background: #fff;
        border-radius: 28px;
        padding: 1.5rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .05);
        border: 1px solid #f1f5f9;
    }

    .main-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .main-card-header h4 {
        margin: 0;
        font-weight: 700;
        font-size: 1.25rem;
        color: #0f172a;
    }

    .main-card-header p {
        margin: 0;
        color: #64748b;
        font-size: 0.9rem;
    }

    .header-buttons {
        display: flex;
        gap: .7rem;
        flex-wrap: wrap;
    }

    {{-- ============================================= --}}
    {{-- BUTTONS --}}
    {{-- ============================================= --}}
    .custom-btn {
        border-radius: 14px;
        padding: .7rem 1.2rem;
        font-weight: 600;
        border: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .custom-btn.btn-primary {
        background: linear-gradient(135deg, #2563eb, #3b82f6);
        color: #fff;
    }

    .custom-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(37, 99, 235, 0.3);
    }

    {{-- ============================================= --}}
    {{-- SEARCH CARD - UPDATED --}}
    {{-- ============================================= --}}
    .search-card {
        background: #f8fafc;
        border-radius: 20px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #eef2f7;
    }

    .search-input-wrapper {
        position: relative;
    }

    .search-input-wrapper i {
        position: absolute;
        top: 50%;
        left: 15px;
        transform: translateY(-50%);
        color: #94a3b8;
        z-index: 5;
        font-size: 1.1rem;
    }

    .custom-input {
        border-radius: 14px !important;
        border: 1px solid #e2e8f0;
        min-height: 48px;
        padding-left: 42px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: #ffffff;
    }

    .custom-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, .10) !important;
    }

    .custom-input::placeholder {
        color: #94a3b8;
        font-size: 0.9rem;
    }

    .custom-input.is-invalid {
        border-color: #ef4444;
    }

    .invalid-feedback {
        font-size: 0.8rem;
        color: #ef4444;
        margin-top: 0.3rem;
    }

    {{-- ============================================= --}}
    {{-- QUICK ACTION CARDS --}}
    {{-- ============================================= --}}
    .quick-action-card {
        background: #f8fafc;
        border-radius: 16px;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        text-decoration: none;
        color: #1e293b;
        transition: all 0.3s ease;
        border: 1px solid #eef2f7;
        cursor: pointer;
    }

    .quick-action-card:hover {
        background: #fff;
        border-color: #2563eb;
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(37, 99, 235, .10);
        text-decoration: none;
        color: #1e293b;
    }

    .quick-action-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .quick-action-card h6 {
        margin: 0;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .quick-action-card small {
        color: #64748b;
        font-size: 0.8rem;
    }

    .quick-action-card .bi-chevron-right {
        color: #94a3b8;
        font-size: 1.2rem;
        transition: all 0.3s ease;
    }

    .quick-action-card:hover .bi-chevron-right {
        color: #2563eb;
        transform: translateX(4px);
    }

    {{-- ============================================= --}}
    {{-- ANIMATIONS --}}
    {{-- ============================================= --}}
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    {{-- ============================================= --}}
    {{-- RESPONSIVE --}}
    {{-- ============================================= --}}
    @media(max-width: 992px) {
        .main-card-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }

    @media(max-width: 768px) {
        .stats-card {
            padding: 1rem;
        }

        .stats-icon {
            width: 48px;
            height: 48px;
            font-size: 1.2rem;
        }

        .stats-card h3 {
            font-size: 1.3rem;
        }

        .main-card {
            padding: 1rem;
            border-radius: 20px;
        }

        .main-card-header {
            flex-direction: column;
            align-items: stretch;
            gap: 0.5rem;
        }

        .main-card-header h4 {
            font-size: 1.1rem;
        }

        .main-card-header p {
            font-size: 0.85rem;
        }

        .search-card {
            padding: 1rem;
        }

        .custom-input {
            min-height: 44px;
            font-size: 0.9rem;
        }

        .header-buttons {
            width: 100%;
        }

        .header-buttons a,
        .header-buttons button {
            flex: 1;
            justify-content: center;
        }

        .quick-action-card {
            padding: 0.8rem 1rem;
        }

        .quick-action-icon {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }

        .quick-action-card h6 {
            font-size: 0.85rem;
        }

        .quick-action-card small {
            font-size: 0.7rem;
        }
    }

    @media(max-width: 576px) {
        .stats-card {
            padding: 0.8rem;
            border-radius: 16px;
        }

        .stats-icon {
            width: 40px;
            height: 40px;
            font-size: 1rem;
            border-radius: 12px;
        }

        .stats-card h3 {
            font-size: 1.1rem;
        }

        .stats-card p {
            font-size: 0.75rem;
        }

        .main-card {
            padding: 0.75rem;
            border-radius: 16px;
        }

        .search-card {
            padding: 0.75rem;
            border-radius: 14px;
        }

        .custom-input {
            min-height: 40px;
            padding-left: 36px;
            font-size: 0.85rem;
        }

        .search-input-wrapper i {
            left: 12px;
            font-size: 0.9rem;
        }

        .custom-btn {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }

        .quick-action-card {
            padding: 0.7rem 0.8rem;
            border-radius: 12px;
        }

        .quick-action-icon {
            width: 36px;
            height: 36px;
            font-size: 0.85rem;
            border-radius: 10px;
        }

        .quick-action-card h6 {
            font-size: 0.8rem;
        }
    }
</style>
@endpush