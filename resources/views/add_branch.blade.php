<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add Branch - Rich Labz CRM</title>

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
                <div class="crm-title">Add New Branch</div>

                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-back d-flex align-items-center gap-1" onclick="history.back()">
                        <i class="bi bi-arrow-left"></i> Back
                    </button>

                    <a href="{{ route('manage-branch') }}" class="create-invoice-btn">
                        Manage Branches
                    </a>
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

            <!-- CARD -->
            <div class="crm-card">
                <form id="addBranchForm" action="{{ route('store-branch') }}" method="POST" autocomplete="off">
                    @csrf

                    <div class="row g-3">

                        <!-- Branch Name -->
                        <div class="col-md-4 input-icon">
                            <label class="form-label">Branch Name <span class="text-danger">*</span></label>
                            <i class="bi bi-building"></i>
                            <input type="text" name="branch_name" value="{{ old('branch_name') }}" class="form-control"
                                placeholder="Enter Branch Name">

                            @error('branch_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Location -->
                        <div class="col-md-4 input-select">
                            <label class="form-label">Location <span class="text-danger">*</span></label>
                            <i class="bi bi-geo-alt left-icon"></i>

                            <select name="location_id" class="form-control">
                                <option value="">Select Location</option>

                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                        {{ $location->location_name }}
                                    </option>
                                @endforeach
                            </select>

                            <i class="bi bi-chevron-down dropdown-icon"></i>

                            @error('location_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-md-4 input-select">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <i class="bi bi-check-circle left-icon"></i>

                            <select class="form-control" name="status">
                                <option value="" disabled selected>Select Status</option>
                                <option value="1" {{ old('status') == "1" ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status') == "0" ? 'selected' : '' }}>Inactive</option>
                            </select>

                            <i class="bi bi-chevron-down dropdown-icon"></i>

                            @error('status')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                    </div>

                    <!-- ACTION BUTTONS -->
                    <div class="crm-action-bar mt-4">
                        <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Save Branch
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>

    @include('includes.footer_links')


</body>

</html>