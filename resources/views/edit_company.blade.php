<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Company - Rich Labz CRM</title>

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
            <div class="crm-title">Edit Company</div>

            <div class="d-flex align-items-center gap-3">
                <button type="button" class="btn btn-back d-flex align-items-center gap-1"
                        onclick="history.back()">
                    <i class="bi bi-arrow-left"></i> Back
                </button>

                <a href="{{ route('manage-company') }}" class="create-invoice-btn">
                    Manage Companies
                </a>
            </div>
        </div>

        <div class="crm-card">
            <form action="{{ route('update-company', $company->id) }}" method="POST" autocomplete="off">
                @csrf

                <div class="row g-3">

                    <div class="col-md-4 input-icon">
                        <label class="form-label">Company Name <span class="text-danger">*</span></label>
                        <i class="bi bi-building"></i>
                        <input type="text" class="form-control" name="company_name"
                               value="{{ old('company_name', $company->company_name) }}" required>
                        @error('company_name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-4 input-icon">
                        <label class="form-label">Contact Person</label>
                        <i class="bi bi-person-circle"></i>
                        <input type="text" class="form-control" name="contact_person"
                               value="{{ old('contact_person', $company->contact_person) }}">
                    </div>

                    <div class="col-md-4 input-icon">
                        <label class="form-label">Phone Number</label>
                        <i class="bi bi-phone"></i>
                        <input type="text" class="form-control" name="phone"
                               value="{{ old('phone', $company->phone) }}">
                    </div>

                    <div class="col-md-4 input-icon">
                        <label class="form-label">Email</label>
                        <i class="bi bi-envelope"></i>
                        <input type="email" class="form-control" name="email"
                               value="{{ old('email', $company->email) }}">
                    </div>

                    <div class="col-md-4 input-select">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <i class="bi bi-check-circle left-icon"></i>
                        <select class="form-control" name="status" required>
                            <option value="1" {{ $company->status == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ $company->status == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                        <i class="bi bi-chevron-down dropdown-icon"></i>
                    </div>

                    <div class="col-md-12 input-icon">
                        <label class="form-label">Address</label>
                        <i class="bi bi-geo-alt"></i>
                        <textarea class="form-control" name="address" rows="3">{{ old('address', $company->address) }}</textarea>
                    </div>

                </div>

                <div class="crm-action-bar mt-4">
                    <button type="button" onclick="history.back()" class="btn btn-outline-secondary">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-primary">
                        Update Company
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>

@include('includes.footer_links')

</body>
</html>
