<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add Company - Rich Labz CRM</title>

    @include('includes.header_links')

    <style>
        .input-icon i,
        .input-select i.left-icon {
            position: absolute;
            left: 12px;
            top: 70%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .input-icon input,
        .input-icon textarea {
            padding-left: 38px !important;
        }

        .input-select {
            position: relative;
        }

        .input-select select {
            padding-left: 38px !important;
            padding-right: 40px !important;
            background: #f9f9fb;
            appearance: none;
        }

        .dropdown-icon {
            position: absolute;
            right: 12px;
            top: 70%;
            transform: translateY(-50%);
            pointer-events: none;
            font-size: 16px;
            color: #6c757d;
        }
    </style>
</head>

<body>

    @include('includes.sidebar')
    @include('includes.header')

    <div class="content">
        <div class="container-crm">

            <div class="crm-header">
                <div class="crm-title">Add Company</div>

                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-back d-flex align-items-center gap-1" onclick="history.back()">
                        <i class="bi bi-arrow-left"></i> Back
                    </button>

                    <a href="{{ route('manage-company') }}" class="create-invoice-btn">Manage Companies</a>
                </div>
            </div>

                       @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        <strong><i class="bi bi-check-circle-fill"></i></strong> 
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <strong><i class="bi bi-exclamation-triangle-fill"></i></strong> 
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

            <div class="crm-card">
                <form action="{{ route('store-company') }}" method="POST" autocomplete="off">
                    @csrf

                    <div class="row g-3">

                        <div class="col-md-4 input-icon">
                            <label class="form-label">Company Name <span class="text-danger">*</span></label>
                            <i class="bi bi-building"></i>
                            <input type="text" class="form-control" name="company_name" placeholder="Enter Company Name"
                                required>
                            @error('company_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 input-icon">
                            <label class="form-label">Contact Person</label>
                            <i class="bi bi-person-circle"></i>
                            <input type="text" class="form-control" name="contact_person" placeholder="Enter Contact Person">
                        </div>

                        <div class="col-md-4 input-icon">
                            <label class="form-label">Phone Number</label>
                            <i class="bi bi-phone"></i>
                            <input type="text" class="form-control" name="phone" placeholder="+91 98765 43210">
                        </div>

                        <div class="col-md-4 input-icon">
                            <label class="form-label">Email</label>
                            <i class="bi bi-envelope"></i>
                            <input type="email" class="form-control" name="email" placeholder="company@email.com">
                        </div>

                        <div class="col-md-4 input-select">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <i class="bi bi-check-circle left-icon"></i>
                            <select class="form-control" name="status" required>
                                <option value="" disabled selected>Select Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            <i class="bi bi-chevron-down dropdown-icon"></i>
                        </div>

                        <div class="col-md-12 input-icon">
                            <label class="form-label">Address</label>
                            <i class="bi bi-geo-alt"></i>
                            <textarea class="form-control" name="address" rows="3"
                                placeholder="Enter Company Address"></textarea>
                        </div>

                    </div>

                    <div class="crm-action-bar mt-4">
                        <button type="button" onclick="history.back()" class="btn btn-outline-secondary">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Company</button>
                    </div>

                </form>
            </div>

        </div>
    </div>

    @include('includes.footer_links')

</body>
</html>
