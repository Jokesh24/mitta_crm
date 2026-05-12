<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Team Member - Rich Labz CRM</title>

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
        }

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

        <!-- PAGE HEADER -->
        <div class="crm-header">
            <div class="crm-title">Edit Team Member</div>

            <div class="d-flex align-items-center gap-3">
                <button type="button" class="btn btn-back d-flex align-items-center gap-1" onclick="history.back()">
                    <i class="bi bi-arrow-left"></i> Back
                </button>

                <a href="{{ route('manage-team') }}" class="create-invoice-btn">Manage Team</a>
            </div>
        </div>

        <!-- EDIT FORM -->
        <div class="crm-card">
            <form action="{{ route('team.update', $team->id) }}" method="POST" autocomplete="off">
                @csrf

                <div class="row g-3">

                    <!-- First Name -->
                    <div class="col-md-4 input-icon">
                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                        <i class="bi bi-person"></i>
                        <input type="text" name="first_name" class="form-control"
                               value="{{ old('first_name', $team->first_name) }}">
                        @error('first_name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Last Name -->
                    <div class="col-md-4 input-icon">
                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                        <i class="bi bi-person"></i>
                        <input type="text" name="last_name" class="form-control"
                               value="{{ old('last_name', $team->last_name) }}">
                        @error('last_name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Employee Code -->
                    <div class="col-md-4 input-icon">
                        <label class="form-label">Employee Code</label>
                        <i class="bi bi-hash"></i>
                        <input type="text" name="emp_code" class="form-control"
                               value="{{ old('emp_code', $team->emp_code) }}">
                        @error('emp_code') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Email -->
                    <div class="col-md-4 input-icon">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <i class="bi bi-envelope"></i>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email', $team->email) }}">
                        @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Mobile -->
                    <div class="col-md-4 input-icon">
                        <label class="form-label">Phone <span class="text-danger">*</span></label>
                        <i class="bi bi-phone"></i>
                        <input type="text" name="mobile" class="form-control"
                               value="{{ old('mobile', $team->mobile) }}">
                        @error('mobile') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Role -->
                    <div class="col-md-4 input-select">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <i class="bi bi-briefcase left-icon"></i>
                        <select name="role" class="form-control">
                            <option value="1" {{ $team->role == 1 ? 'selected' : '' }}>Admin</option>
                            <option value="2" {{ $team->role == 2 ? 'selected' : '' }}>Lab Technician</option>
                        </select>
                        @error('role') <small class="text-danger">{{ $message }}</small> @enderror
                        <i class="bi bi-chevron-down dropdown-icon"></i>
                    </div>

                    <!-- Branch -->
                    <div class="col-md-4 input-select">
                        <label class="form-label">Branch <span class="text-danger">*</span></label>
                        <i class="bi bi-building left-icon"></i>
                        <select name="branch_id" class="form-control">
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}" {{ $team->branch_id == $b->id ? 'selected' : '' }}>
                                    {{ $b->branch_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('branch_id') <small class="text-danger">{{ $message }}</small> @enderror
                        <i class="bi bi-chevron-down dropdown-icon"></i>
                    </div>

                    <!-- Join Date -->
                    <div class="col-md-4 input-icon">
                        <label class="form-label">Date of Join <span class="text-danger">*</span></label>
                        <i class="bi bi-calendar"></i>
                        <input type="text" name="date_of_join" class="form-control datepicker"
                               value="{{ old('date_of_join', \Carbon\Carbon::parse($team->date_of_join)->format('d-m-Y')) }}">
                        @error('date_of_join') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-md-4 input-select">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <i class="bi bi-check-circle left-icon"></i>
                        <select name="status" class="form-control">
                            <option value="1" {{ $team->status == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ $team->status == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                        <i class="bi bi-chevron-down dropdown-icon"></i>
                    </div>

                    <!-- Address -->
                    <div class="col-12 input-icon">
                        <label class="form-label">Address</label>
                        <i class="bi bi-geo-alt"></i>
                        <textarea name="address" class="form-control" rows="3">{{ old('address', $team->address) }}</textarea>
                        @error('address') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                </div>

                <!-- ACTION BUTTONS -->
                <div class="crm-action-bar mt-4">
                    <button type="button" class="btn btn-outline-secondary" onclick="history.back()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Team Member</button>
                </div>

            </form>
        </div>

    </div>
</div>

@include('includes.footer_links')

<script>
    $(function () {
        $(".datepicker").datepicker({
            dateFormat: "dd-mm-yy",
            changeMonth: true,
            changeYear: true
        });
    });
</script>

</body>

</html>
