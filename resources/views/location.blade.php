<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add Location - Rich Labz CRM</title>

    @include('includes.header_links')

    <style>
        .input-icon i,
        .input-select i.left-icon {
            position: absolute;
            left: 20px;
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
            appearance: none;
            background: #f9f9fb;
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
            <div class="crm-title">Add New Location</div>

            <div class="d-flex align-items-center gap-3">

                <button type="button" onclick="history.back()" class="btn btn-back d-flex gap-1">
                    <i class="bi bi-arrow-left"></i> Back
                </button>

                <a href="{{ route('manage-locations') }}" class="create-invoice-btn">
                    Manage Locations
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

        <div class="crm-card">
            <form action="{{ route('store-location') }}" method="POST" autocomplete="off">
                @csrf

                <div class="row mb-3">

                    <div class="col-md-4 input-icon">
                        <label class="form-label">Location Name <span class="text-danger">*</span></label>
                        <i class="bi bi-geo-alt-fill"></i>
                        <input type="text" class="form-control" name="location_name"
                               value="{{ old('location_name') }}" >
                        @error('location_name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-4 input-select">
                        <label class="form-label">State <span class="text-danger">*</span></label>
                        <i class="bi bi-buildings left-icon"></i>

                        <select class="form-control" name="state" >
                            <option value="" disabled selected>Select State</option>

                            @foreach(['Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh',
                                'Goa','Gujarat','Haryana','Himachal Pradesh','Jharkhand',
                                'Karnataka','Kerala','Madhya Pradesh','Maharashtra','Manipur',
                                'Meghalaya','Mizoram','Nagaland','Odisha','Punjab','Rajasthan',
                                'Sikkim','Tamil Nadu','Telangana','Tripura','Uttar Pradesh',
                                'Uttarakhand','West Bengal'] as $state)
                                <option value="{{ $state }}">{{ $state }}</option>
                            @endforeach
                        </select>

                        @error('state') <span class="text-danger">{{ $message }}</span> @enderror

                        <i class="bi bi-chevron-down dropdown-icon"></i>
                    </div>

                    <div class="col-md-4 input-icon">
                        <label class="form-label">Pincode</label>
                        <i class="bi bi-hash"></i>
                        <input type="text" class="form-control" name="pincode"
                               value="{{ old('pincode') }}">
                    </div>

                    <div class="col-md-4 input-select">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <i class="bi bi-check2-circle left-icon"></i>

                        <select class="form-control" name="status" >
                            <option value="" disabled selected>Select Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>

                        @error('status') <span class="text-danger">{{ $message }}</span> @enderror

                        <i class="bi bi-chevron-down dropdown-icon"></i>
                    </div>

                </div>

                <div class="crm-action-bar mt-4">
                    <button type="button" onclick="history.back()" class="btn btn-outline-secondary">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-primary">Save Location</button>
                </div>

            </form>
        </div>

    </div>
</div>

@include('includes.footer_links')

</body>
</html>
