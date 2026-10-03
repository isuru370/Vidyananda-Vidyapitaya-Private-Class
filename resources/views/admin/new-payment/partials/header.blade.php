<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold">
            <i class="bi bi-credit-card-2-front-fill me-2 text-primary"></i>
            Payment Management
        </h4>
        <p class="text-muted mb-0">
            QR / Student ID → Payment → Auto Print
        </p>
    </div>

    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-success">
            Fast Payment
        </span>

        <div class="hero-actions">
            <a href="{{ route('admin.payments.today-receipt') }}" class="btn btn-light border custom-btn">
                <i class="bi bi-receipt me-1"></i>
                Today's Receipts
            </a>
        </div>
    </div>
</div>
