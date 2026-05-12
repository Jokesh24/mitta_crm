<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Branch - Rich Labz CRM</title>

    @include('includes.header_links')

    <style>
        /* LEFT ICON */
        .input-icon i,
        .input-select i.left-icon {
            position: absolute;
            left: 12px;
            top: 70%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        /* INPUT SPACING */
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
            appearance: none;
            background: #f9f9fb;
        }

        .dropdown-icon {
            position: absolute;
            right: 12px;
            top: 70%;
            transform: translateY(-50%);
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
            <div class="crm-title">Edit Branch</div>

            <div class="d-flex align-items-center gap-3">
                <button type="button" class="btn btn-back d-flex align-items-center gap-1" onclick="history.back()">
                    <i class="bi bi-arrow-left"></i> Back
                </button>

                <a href="{{ route('manage-branch') }}" class="create-invoice-btn">Manage Branches</a>
            </div>
        </div>

        <!-- EDIT FORM CARD -->
        <div class="crm-card">
            <form action="{{ route('update-branch', $branch->id) }}" method="POST" autocomplete="off">
                @csrf

                <div class="row g-3">

                    <!-- Branch Name -->
                    <div class="col-md-4 input-icon">
                        <label class="form-label">Branch Name <span class="text-danger">*</span></label>
                        <i class="bi bi-building"></i>

                        <input 
                            type="text" 
                            name="branch_name"
                            value="{{ old('branch_name', $branch->branch_name) }}"
                            class="form-control" 
                            required
                        >

                        @error('branch_name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Location Dropdown -->
                    <div class="col-md-4 input-select">
                        <label class="form-label">Location <span class="text-danger">*</span></label>
                        <i class="bi bi-geo-alt left-icon"></i>

                        <select name="location_id" class="form-control" required>
                            <option value="">Select Location</option>

                            @foreach($locations as $location)
                                <option value="{{ $location->id }}"
                                    {{ old('location_id', $branch->location_id) == $location->id ? 'selected' : '' }}>
                                    {{ $location->location_name }}
                                </option>
                            @endforeach
                        </select>

                        <i class="bi bi-chevron-down dropdown-icon"></i>

                        @error('location_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-md-4 input-select">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <i class="bi bi-check-circle left-icon"></i>

                        <select name="status" class="form-control" required>
                            <option value="" disabled>Select Status</option>
                            <option value="1" {{ old('status', $branch->status) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', $branch->status) == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>

                        <i class="bi bi-chevron-down dropdown-icon"></i>

                        @error('status')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                </div>

                <!-- BUTTONS -->
                <div class="crm-action-bar mt-4">
                    <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-primary">
                        Update Branch
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>

@include('includes.footer_links')

</body>

</html>
