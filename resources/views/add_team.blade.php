<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add Team Member - Rich Labz CRM</title>
    @include('includes.header_links')

    <style>
        /* LEFT ICON (for inputs & selects) */
        .input-icon i,
        .input-select i.left-icon {
            position: absolute;
            left: 12px;
            top: 70%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        /* TEXT INPUT + LEFT ICON */
        .input-icon input {
            padding-left: 38px !important;
        }

        /* TEXTAREA + LEFT ICON */
        .input-icon textarea {
            padding-left: 38px !important;
        }

        /* SELECT WRAPPER */
        .input-select {
            position: relative;
        }

        /* SELECT FIELD */
        .input-select select {
            padding-left: 38px !important;
            padding-right: 40px !important;
            background: #f9f9fb;
            appearance: none;
        }

        /* RIGHT DROPDOWN ICON */
        .input-select i.dropdown-icon {
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

            <!-- Page Header -->
            <div class="crm-header">
                <div class="crm-title">Add New Team Member</div>

                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-back d-flex align-items-center gap-1" onclick="history.back()">
                        <i class="bi bi-arrow-left"></i> Back
                    </button>
                    <a href="{{ route('manage-team') }}" class="create-invoice-btn">Manage Team</a>
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

            <!-- Add Team Form -->
            <div class="crm-card">
                <form action="{{ route('team.store') }}" method="POST" autocomplete="off">
                    @csrf

                    <div class="row g-3">

                        <!-- First Name -->
                        <div class="col-md-4 input-icon">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <i class="bi bi-person"></i>
                            <input type="text" class="form-control" name="first_name" value="{{ old('first_name') }}"
                                placeholder="Amit">
                            @error('first_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Last Name -->
                        <div class="col-md-4 input-icon">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <i class="bi bi-person"></i>
                            <input type="text" class="form-control" name="last_name" value="{{ old('last_name') }}"
                                placeholder="Sharma">
                            @error('last_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Employee Code -->
                        <div class="col-md-4 input-icon">
                            <label class="form-label">Employee Code</label>
                            <i class="bi bi-hash"></i>
                            <input type="text" class="form-control" name="emp_code" value="{{ old('emp_code') }}"
                                placeholder="EMP-001">
                            @error('emp_code') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-4 input-icon">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <i class="bi bi-envelope"></i>
                            <input type="email" class="form-control" name="email" value="{{ old('email') }}"
                                placeholder="user@company.com">
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Mobile -->
                        <div class="col-md-4 input-icon">
                            <label class="form-label">Phone <span class="text-danger">*</span></label>
                            <i class="bi bi-phone"></i>
                            <input type="text" class="form-control" name="mobile" value="{{ old('mobile') }}"
                                placeholder="9876543210">
                            @error('mobile') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Role -->
                        <div class="col-md-4 input-select">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <i class="bi bi-briefcase left-icon"></i>
                            <select name="role" class="form-control">
                                <option value="" disabled selected>Select Role</option>
                                <option value="1" {{ old('role') == 1 ? 'selected' : '' }}>Admin</option>
                                <option value="2" {{ old('role') == 2 ? 'selected' : '' }}>Lab Technician</option>
                            </select>
                            @error('role') <small class="text-danger">{{ $message }}</small> @enderror
                            <i class="bi bi-chevron-down dropdown-icon"></i>
                        </div>

                        <!-- Branch -->
                        <div class="col-md-4 input-select">
                            <label class="form-label">Branch <span class="text-danger">*</span></label>
                            <i class="bi bi-building left-icon"></i>
                            <select name="branch_id" class="form-control">
                                <option value="" disabled selected>Select Branch</option>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>
                                        {{ $b->branch_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id') <small class="text-danger">{{ $message }}</small> @enderror
                            <i class="bi bi-chevron-down dropdown-icon"></i>
                        </div>

                        <!-- Date of Join -->
                        <div class="col-md-4 input-icon">
                            <label class="form-label">Date of Join <span class="text-danger">*</span></label>
                            <i class="bi bi-calendar"></i>
                            <input type="text" class="form-control datepicker" name="date_of_join"
                                value="{{ old('date_of_join') }}" placeholder="Select Date">
                            @error('date_of_join') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-md-4 input-select">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <i class="bi bi-check-circle left-icon"></i>
                            <select name="status" class="form-control">
                                <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status', 1) == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>

                            @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                            <i class="bi bi-chevron-down dropdown-icon"></i>
                        </div>

                        <!-- Address -->
                        <div class="col-12 input-icon">
                            <label class="form-label">Address</label>
                            <i class="bi bi-geo-alt"></i>
                            <textarea class="form-control" name="address" rows="3">{{ old('address') }}</textarea>
                            @error('address') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                    </div>

                    <div class="crm-action-bar mt-4">
                        <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Save Team Member
                        </button>
                    </div>
                </form>

            </div>

        </div>
    </div>

    @include('includes.footer_links')


    <!-- jQuery Datepicker Script -->
    <script>
        $(function () {
            $(".datepicker").datepicker({
                dateFormat: "dd-mm-yy",
                changeMonth: true,
                changeYear: true,
                showOtherMonths: true,
                selectOtherMonths: true
            });
        });
    </script>

</body>

</html>