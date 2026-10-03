<aside class="sidebar" id="sidebar">

    <!-- SIDEBAR HEADER -->
    <div class="sidebar-header">

        <button type="button" class="brand text-decoration-none" id="brandButton"
            style="background: none; border: none; width: 100%; cursor: pointer; padding: 0;">

            <div class="brand-icon">
                N
            </div>

            <div class="brand-text">

                <h4>Nexora</h4>

                <small>
                    Education System
                </small>

            </div>

        </button>

    </div>

    <!-- SIDEBAR BODY -->
    <div class="sidebar-body">

        <!-- MAIN SECTION -->
        <div class="sidebar-section">
            <div class="sidebar-section-title">MAIN MENU</div>

            @if (hasPermission('dashboard'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                        data-route="admin.dashboard" data-href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </button>
                </div>
            @endif
            @if (hasPermission('weekly-timetable'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.weekly-timetable') ? 'active' : '' }}"
                        data-route="weekly-timetable" data-href="{{ route('admin.weekly-timetable') }}">
                        <i class="bi bi-calendar3"></i>
                        <span>Timetable</span>
                    </button>
                </div>
            @endif
            @if (hasPermission('attendance.index'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.class-schedules.todayClasses') ? 'active' : '' }}"
                        data-route="admin.class-schedules.todayClasses"
                        data-href="{{ route('admin.class-schedules.todayClasses') }}">
                        <i class="bi bi-calendar2-check-fill"></i>
                        <span>Today's Classes</span>
                    </button>
                </div>
            @endif

        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-title">STUDENT SERVICES</div>

            @if (hasPermission('new-attendance.index'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.new-attendance.index') ? 'active' : '' }}"
                        data-route="admin.new-attendance.index" data-href="{{ route('admin.new-attendance.index') }}">
                        <i class="bi bi-calendar2-check-fill"></i>
                        <span>Attendance</span>
                    </button>
                </div>
            @endif

            @if (hasPermission('new-payment.index'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.new-payment.index') ? 'active' : '' }}"
                        data-route="admin.new-payment.index" data-href="{{ route('admin.new-payment.index') }}">
                        <i class="bi bi-credit-card-fill"></i>
                        <span>Payments</span>
                    </button>
                </div>
            @endif

            @if (hasPermission('payments.today-receipt'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.payments.today-receipt') ? 'active' : '' }}"
                        data-route="admin.payments.today-receipt"
                        data-href="{{ route('admin.payments.today-receipt') }}">
                        <i class="bi bi-piggy-bank-fill"></i>
                        <span>Payments History</span>
                    </button>
                </div>
            @endif

        </div>

        {{-- <!-- NOTIFICATION SECTION -->
        <div class="sidebar-section">
            <div class="sidebar-section-title">NOTIFICATION</div>

            @if (hasPermission('notifications.view') ||
                    hasPermission('notifications.create') ||
                    hasPermission('notifications.delete'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.notifications*') ? 'active' : '' }}"
                        data-route="admin.notifications" data-href="{{ route('admin.notifications.index') }}">
                        <i class="bi bi-bell"></i>
                        <span>Notifications</span>
                        @php
                            $unreadCount = App\Models\Notification::unread()->count();
                        @endphp
                        @if ($unreadCount > 0)
                            <span class="badge badge-danger ml-auto">{{ $unreadCount }}</span>
                        @endif
                    </button>
                </div>
            @endif
        </div> --}}

        <!-- MANAGEMENT -->
        <div class="sidebar-section">
            <div class="sidebar-section-title">MANAGEMENT</div>

            @if (hasPermission('students.index'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.students.*') ? 'active' : '' }}"
                        data-route="admin.students.index" data-href="{{ route('admin.students.index') }}">
                        <i class="bi bi-people-fill"></i>
                        <span>Students</span>
                    </button>
                </div>
            @endif

            @if (hasPermission('teachers.index'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.teachers.*') ? 'active' : '' }}"
                        data-route="admin.teachers.index" data-href="{{ route('admin.teachers.index') }}">
                        <i class="bi bi-person-badge-fill"></i>
                        <span>Teachers</span>
                    </button>
                </div>
            @endif

            @if (hasPermission('organizers.index'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.organizers.*') ? 'active' : '' }}"
                        data-route="admin.organizers.index" data-href="{{ route('admin.organizers.index') }}">
                        <i class="bi bi-calendar-check-fill"></i>
                        <span>Organizers</span>
                    </button>
                </div>
            @endif
            @if (hasPermission('student-class-management.index'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.student-class-management.*') ? 'active' : '' }}"
                        data-route="admin.student-class-management.index"
                        data-href="{{ route('admin.student-class-management.index') }}">
                        <i class="bi bi-person-workspace"></i>
                        <span>Student Class Mgmt</span>
                    </button>
                </div>
            @endif

            @if (hasPermission('system-users.index'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.system-users.*') ? 'active' : '' }}"
                        data-route="admin.system-users.index" data-href="{{ route('admin.system-users.index') }}">
                        <i class="bi bi-people-fill"></i>
                        <span>System User</span>
                    </button>
                </div>
            @endif

            @if (hasPermission('users.index'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                        data-route="admin.users.index" data-href="{{ route('admin.users.index') }}">
                        <i class="bi bi-person-circle"></i>
                        <span>Users</span>
                    </button>
                </div>
            @endif
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-title">STUDENT SERVICES</div>

            @if (hasPermission('student-images.index'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.student-images.*') ? 'active' : '' }}"
                        data-route="admin.student-images.index"
                        data-href="{{ route('admin.student-images.index') }}">
                        <i class="bi bi-images"></i>
                        <span>Student Images</span>
                    </button>
                </div>
            @endif

            {{-- @if (hasPermission('student-id-cards.index'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.student-id-cards.index') ? 'active' : '' }}"
                        data-route="admin.student-id-cards.index"
                        data-href="{{ route('admin.student-id-cards.index') }}">
                        <i class="bi bi-card-heading"></i>
                        <span>Student ID Cards</span>
                    </button>
                </div>
            @endif --}}
            @if (hasPermission('student-cards.index'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.student-cards.*') ? 'active' : '' }}"
                        data-route="admin.student-cards.index" data-href="{{ route('admin.student-cards.index') }}">

                        <i class="bi bi-person-vcard-fill"></i>

                        <span>Student Cards</span>

                    </button>
                </div>
            @endif

            {{-- @if (hasPermission('admin.temporary-id-cards.index'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.temporary-id-cards.*') ? 'active' : '' }}"
                        data-route="admin.temporary-id-cards.index"
                        data-href="{{ route('admin.temporary-id-cards.index') }}">
                        <i class="bi bi-person-badge-fill"></i>
                        <span>Temporary ID</span>
                    </button>
                </div>
            @endif --}}
        </div>


        <!-- ACADEMIC -->
        <div class="sidebar-section">
            <div class="sidebar-section-title">ACADEMIC</div>

            @if (hasPermission('student-classes.index'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.student-classes.*') ? 'active' : '' }}"
                        data-route="admin.student-classes.index"
                        data-href="{{ route('admin.student-classes.index') }}">
                        <i class="bi bi-book-fill"></i>
                        <span>Classes</span>
                    </button>
                </div>
            @endif

            @if (hasPermission('class-schedules.index'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.class-schedules.*') ? 'active' : '' }}"
                        data-route="admin.class-schedules.index"
                        data-href="{{ route('admin.class-schedules.index') }}">
                        <i class="bi bi-calendar-event-fill"></i>
                        <span>Class Schedule</span>
                    </button>
                </div>
            @endif

            @if (hasPermission('student-class-enrollments.index'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.student-class-enrollments.*') ? 'active' : '' }}"
                        data-route="admin.student-class-enrollments.index"
                        data-href="{{ route('admin.student-class-enrollments.index') }}">
                        <i class="bi bi-pencil-square"></i>
                        <span>Enrollments</span>
                    </button>
                </div>
            @endif

        </div>

        <!-- EXAM -->
        <div class="sidebar-section">
            <div class="sidebar-section-title">EXAM</div>

            @if (hasPermission('exams.index'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.exams.*') ? 'active' : '' }}"
                        data-route="admin.exams.index" data-href="{{ route('admin.exams.index') }}">
                        <i class="bi bi-calendar-check"></i>
                        <span>All Exams</span>
                    </button>
                </div>
            @endif
        </div>

        <!-- STUDENT FINANCE -->
        <div class="sidebar-section">
            <div class="sidebar-section-title">STUDENT FINANCE</div>

            @if (hasPermission('payments.index'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.admissions.*') ? 'active' : '' }}"
                        data-route="admin.admissions.index" data-href="{{ route('admin.admissions.index') }}">
                        <i class="bi bi-credit-card-fill"></i>
                        <span>Admissions</span>
                    </button>
                </div>
            @endif

            @if (hasPermission('payments.index'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.admission-payments.*') ? 'active' : '' }}"
                        data-route="admin.admission-payments.index"
                        data-href="{{ route('admin.admission-payments.index') }}">
                        <i class="bi bi-credit-card-fill"></i>
                        <span>Admissions Payments</span>
                    </button>
                </div>
            @endif

            {{-- @if (hasPermission('payments.today-receipt'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.payments.today-receipt') ? 'active' : '' }}"
                        data-route="admin.payments.today-receipt"
                        data-href="{{ route('admin.payments.today-receipt') }}">
                        <i class="bi bi-credit-card-fill"></i>
                        <span>Today's Payments</span>
                    </button>
                </div>
            @endif --}}

            @if (hasPermission('payment-reminder.index'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.payment-reminder.*') ? 'active' : '' }}"
                        data-route="admin.payment-reminder.index"
                        data-href="{{ route('admin.payment-reminder.index') }}">
                        <i class="bi bi-bell-fill"></i>
                        <span>Payment Reminder</span>
                        @php
                            // Optional: Show count of unpaid students
                            // This is just a visual indicator - you can implement logic
                            $unpaidCount = 0;
                            // You can add logic here to count unpaid students
                        @endphp
                        @if ($unpaidCount > 0)
                            <span class="badge badge-danger ml-auto">{{ $unpaidCount }}</span>
                        @endif
                    </button>
                </div>
            @endif

        </div>

        <!-- INSTITUTE FINANCE -->
        <div class="sidebar-section">
            <div class="sidebar-section-title"> INSTITUTE FINANCE</div>

            @if (hasPermission('teacher-salaries.index'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.teacher-salaries.*') ? 'active' : '' }}"
                        data-route="admin.teacher-salaries.index"
                        data-href="{{ route('admin.teacher-salaries.index') }}">
                        <i class="bi bi-cash-stack"></i>
                        <span>Teacher Salaries</span>
                    </button>
                </div>
            @endif

            @if (hasPermission('organizer-payments.index'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.organizer-payments.index') ? 'active' : '' }}"
                        data-route="admin.organizer-payments.index"
                        data-href="{{ route('admin.organizer-payments.index') }}">
                        <i class="bi bi-wallet2"></i>
                        <span>Organizer Payments</span>
                    </button>
                </div>
            @endif

            @if (hasPermission('extra-incomes.index'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.extra-incomes.*') ? 'active' : '' }}"
                        data-route="admin.extra-incomes.index" data-href="{{ route('admin.extra-incomes.index') }}">
                        <i class="bi bi-cash-coin"></i>
                        <span>Extra Incomes</span>
                    </button>
                </div>
            @endif

            @if (hasPermission('institute-expenses.index'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.institute-expenses.*') ? 'active' : '' }}"
                        data-route="admin.institute-expenses.index"
                        data-href="{{ route('admin.institute-expenses.index') }}">
                        <i class="bi bi-cash-coin"></i>
                        <span>Institute Expenses</span>
                    </button>
                </div>
            @endif

            @if (hasPermission('institute-income.monthly-report'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.institute-income.monthly-report') ? 'active' : '' }}"
                        data-route="admin.institute-income.monthly-report"
                        data-href="{{ route('admin.institute-income.monthly-report') }}">
                        <i class="bi bi-bar-chart-line-fill"></i>
                        <span>Institute Income</span>
                    </button>
                </div>
            @endif


        </div>

        <!-- RECEIPTS -->
        <div class="sidebar-section">
            <div class="sidebar-section-title">RECEIPTS</div>

            @if (hasPermission('receipts.index'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.receipts.*') ? 'active' : '' }}"
                        data-route="admin.receipts.index" data-href="{{ route('admin.receipts.index') }}">
                        <i class="bi bi-file-earmark-person"></i>
                        <span>Receipts</span>
                    </button>
                </div>
            @endif
        </div>

        <!-- REPORTS -->
        <div class="sidebar-section">
            <div class="sidebar-section-title">REPORTS</div>

            @if (hasPermission('daily-report.index'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.daily-report.*') ? 'active' : '' }}"
                        data-route="admin.daily-report.index" data-href="{{ route('admin.daily-report.index') }}">
                        <i class="bi bi-file-earmark-bar-graph"></i>
                        <span>Daily Report</span>
                    </button>
                </div>
            @endif

            @if (hasPermission('monthly-report.index'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.monthly-report.*') ? 'active' : '' }}"
                        data-route="admin.monthly-report.index"
                        data-href="{{ route('admin.monthly-report.index') }}">
                        <i class="bi bi-file-earmark-bar-graph"></i>
                        <span>Monthly Report</span>
                    </button>
                </div>
            @endif


            @if (hasPermission('teacher-report.index'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.teacher-report.*') ? 'active' : '' }}"
                        data-route="admin.teacher-report.index"
                        data-href="{{ route('admin.teacher-report.index') }}">
                        <i class="bi bi-file-earmark-person"></i>
                        <span>Teacher Daily Report</span>
                    </button>
                </div>
            @endif

            @if (hasPermission('institute-reports.index'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.institute-reports.*') ? 'active' : '' }}"
                        data-route="admin.institute-reports.index"
                        data-href="{{ route('admin.institute-reports.index') }}">
                        <i class="bi bi-file-earmark-person"></i>
                        <span>Institute Payment Report</span>
                    </button>
                </div>
            @endif
            {{-- Monthly Class Attendance Report --}}
            @if (hasPermission('monthly-class-attendance-report.index'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.monthly-class-attendance-report.*') ? 'active' : '' }}"
                        data-route="admin.monthly-class-attendance-report.index"
                        data-href="{{ route('admin.monthly-class-attendance-report.index') }}">

                        <i class="bi bi-calendar-check"></i>

                        <span>Monthly Class Attendance Report</span>

                    </button>
                </div>
            @endif

            @if (hasPermission('payment-collection-report.index'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.payment-collection-report.*') ? 'active' : '' }}"
                        data-route="admin.payment-collection-report.index"
                        data-href="{{ route('admin.payment-collection-report.index') }}">

                        <i class="bi bi-cash-stack"></i>

                        <span>Payment Collection Report</span>

                    </button>
                </div>
            @endif
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-title">RECENT ACTIVITIES</div>

            @if (hasPermission('activity-logs.index'))
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}"
                        data-route="admin.activity-logs.index" data-href="{{ route('admin.activity-logs.index') }}">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Activities</span>
                    </button>
                </div>
            @endif
            @if (auth()->check() && auth()->user()->isSuperAdmin())
                <div class="nav-item">
                    <button type="button"
                        class="nav-link-custom {{ request()->routeIs('admin.logs.laravel.*') ? 'active' : '' }}"
                        data-route="admin.logs.laravel.index" data-href="{{ route('logs.laravel.index') }}">

                        <i class="bi bi-file-earmark-text"></i>

                        <span>Laravel Logs</span>

                    </button>
                </div>
            @endif
        </div>

    </div>

</aside>

<style>
    /* SIDEBAR */

    .sidebar {

        width: 280px;

        background:
            linear-gradient(180deg,
                #0f172a 0%,
                #111827 50%,
                #1e293b 100%);

        position: fixed;

        top: 0;
        left: 0;
        bottom: 0;

        z-index: 1030;

        display: flex;
        flex-direction: column;

        border-right:
            1px solid rgba(255, 255, 255, 0.05);

        box-shadow:
            6px 0 25px rgba(0, 0, 0, 0.25);
    }

    /* HEADER */

    .sidebar-header {

        padding: 1.5rem 1.25rem;

        border-bottom:
            1px solid rgba(255, 255, 255, 0.08);

        flex-shrink: 0;
    }

    /* BODY */

    .sidebar-body {

        flex: 1;

        overflow-y: auto;

        padding: 1rem;

        scrollbar-width: thin;
    }

    /* SCROLLBAR */

    .sidebar-body::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar-body::-webkit-scrollbar-thumb {

        background:
            rgba(255, 255, 255, 0.15);

        border-radius: 20px;
    }

    /* BRAND */

    .brand {

        display: flex;
        align-items: center;
        gap: 14px;
    }

    .brand-icon {

        width: 48px;
        height: 48px;

        border-radius: 14px;

        background:
            linear-gradient(135deg,
                #3b82f6,
                #2563eb);

        display: flex;
        align-items: center;
        justify-content: center;

        color: white;

        font-size: 1.4rem;
        font-weight: 800;

        box-shadow:
            0 10px 20px rgba(37, 99, 235, 0.35);
    }

    .brand-text h4 {

        margin: 0;

        color: white;

        font-size: 1.2rem;
        font-weight: 700;
    }

    .brand-text small {

        color: #94a3b8;

        font-size: 0.75rem;
    }

    /* SECTIONS */

    .sidebar-section {
        margin-bottom: 1.8rem;
    }

    .sidebar-section-title {

        color: #64748b;

        font-size: 0.72rem;

        font-weight: 700;

        letter-spacing: 1px;

        margin-bottom: 0.8rem;

        padding-left: 0.8rem;
    }

    /* NAV ITEMS */

    .nav-item {
        margin-bottom: 0.35rem;
    }

    .nav-link-custom {

        display: flex;
        align-items: center;
        gap: 14px;

        padding: 0.9rem 1rem;

        border-radius: 14px;

        color: #cbd5e1;

        text-decoration: none;

        font-size: 0.95rem;
        font-weight: 500;

        transition: all 0.25s ease;

        /* Button reset styles - keeps original look */
        background: none;
        border: none;
        width: 100%;
        cursor: pointer;
        text-align: left;
        font-family: inherit;
    }

    .nav-link-custom i {

        font-size: 1.15rem;

        width: 22px;
    }

    .nav-link-custom:hover {

        background:
            rgba(255, 255, 255, 0.08);

        color: white;

        transform: translateX(5px);
    }

    .nav-link-custom.active {

        background:
            linear-gradient(135deg,
                #2563eb,
                #1d4ed8);

        color: white;

        box-shadow:
            0 10px 18px rgba(37, 99, 235, 0.30);
    }

    /* MOBILE */

    @media(max-width: 991px) {

        .sidebar {
            transform: translateX(-100%);
            transition: 0.3s ease;
        }

        .sidebar.show {
            transform: translateX(0);
        }
    }
</style>

<script>
    (function() {

        const sidebarBody = document.querySelector('.sidebar-body');
        const navButtons = document.querySelectorAll('#sidebar .nav-link-custom');
        const brandButton = document.getElementById('brandButton');

        const SIDEBAR_SCROLL_KEY = 'nexora_sidebar_scroll';

        /*
         * Restore sidebar scroll position after page reload
         */
        function restoreSidebarScroll() {
            if (!sidebarBody) {
                return;
            }

            const savedScroll = sessionStorage.getItem(SIDEBAR_SCROLL_KEY);

            if (savedScroll !== null) {
                setTimeout(function() {
                    sidebarBody.scrollTop = parseInt(savedScroll, 10) || 0;
                }, 50);
            }
        }

        /*
         * Save sidebar scroll position before navigation
         */
        function saveSidebarScroll() {
            if (!sidebarBody) {
                return;
            }

            sessionStorage.setItem(
                SIDEBAR_SCROLL_KEY,
                sidebarBody.scrollTop
            );
        }

        /*
         * Navigation
         */
        function navigateTo(url, routeName, buttonElement) {

            /*
             * Save current sidebar position
             */
            saveSidebarScroll();

            /*
             * Remove active class
             */
            navButtons.forEach(function(btn) {
                btn.classList.remove('active');
            });

            /*
             * Add active class
             */
            if (buttonElement) {
                buttonElement.classList.add('active');
            }

            /*
             * Navigate
             */
            if (url && url !== '#') {
                window.location.href = url;
            }
        }

        /*
         * Sidebar button clicks
         */
        navButtons.forEach(function(button) {

            button.addEventListener('click', function(e) {

                e.preventDefault();

                const url = this.getAttribute('data-href');
                const routeName = this.getAttribute('data-route');

                navigateTo(url, routeName, this);
            });

        });

        /*
         * Brand / Nexora button
         */
        if (brandButton) {

            brandButton.addEventListener('click', function(e) {

                e.preventDefault();

                /*
                 * Save sidebar position
                 */
                saveSidebarScroll();

                const dashboardUrl = "{{ route('admin.dashboard') }}";

                navButtons.forEach(function(btn) {
                    btn.classList.remove('active');
                });

                const dashboardBtn = Array.from(navButtons).find(function(btn) {
                    return btn.getAttribute('data-route') === 'admin.dashboard';
                });

                if (dashboardBtn) {
                    dashboardBtn.classList.add('active');
                }

                if (dashboardUrl && dashboardUrl !== '#') {
                    window.location.href = dashboardUrl;
                }

            });

        }

        /*
         * Restore after page loaded
         */
        restoreSidebarScroll();

    })();
</script>
