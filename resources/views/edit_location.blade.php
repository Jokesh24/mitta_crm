<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Location - Rich Labz CRM</title>

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

        .input-select select {
            padding-left: 38px !important;
            padding-right: 40px !important;
            background: #f9f9fb;
        }
    </style>

</head>
<body>

@include('includes.sidebar')
@include('includes.header')


<div class="content">
    <div class="container-crm">

        <div class="crm-header">
            <div class="crm-title">Edit Location</div>

            <div class="d-flex align-items-center gap-3">
                <button type="button" onclick="history.back()" class="btn btn-back d-flex gap-1">
                    <i class="bi bi-arrow-left"></i> Back
                </button>

                <a href="{{ route('manage-locations') }}" class="create-invoice-btn">
                    Manage Locations
                </a>
            </div>
        </div>

        <div class="crm-card">
            <form action="{{ route('update-location', $location->id) }}" method="POST" autocomplete="off">
                @csrf

                <div class="row mb-3">

                    <div class="col-md-4 input-icon">
                        <label class="form-label">Location Name <span class="text-danger">*</span></label>
                        <i class="bi bi-geo-alt-fill"></i>
                        <input type="text" class="form-control"
                               name="location_name"
                               value="{{ old('location_name', $location->location_name) }}" required>
                    </div>

                    <div class="col-md-4 input-select">
                        <label class="form-label">State <span class="text-danger">*</span></label>
                        <i class="bi bi-buildings left-icon"></i>

                        <select class="form-control" name="state" required>
                            <option disabled>Select State</option>

                            @php
                                $states = [
                                    "Andhra Pradesh", "Arunachal Pradesh", "Assam", "Bihar", "Chhattisgarh",
                                    "Goa", "Gujarat", "Haryana", "Himachal Pradesh", "Jharkhand",
                                    "Karnataka", "Kerala", "Madhya Pradesh", "Maharashtra", "Manipur",
                                    "Meghalaya", "Mizoram", "Nagaland", "Odisha", "Punjab", "Rajasthan",
                                    "Sikkim", "Tamil Nadu", "Telangana", "Tripura",
                                    "Uttar Pradesh", "Uttarakhand", "West Bengal"
                                ];
                            @endphp

                            @foreach($states as $state)
                                <option value="{{ $state }}" {{ $location->state == $state ? 'selected' : '' }}>
                                    {{ $state }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 input-icon">
                        <label class="form-label">Pincode</label>
                        <i class="bi bi-hash"></i>
                        <input type="text" class="form-control"
                               name="pincode"
                               value="{{ old('pincode', $location->pincode) }}">
                    </div>

                    <div class="col-md-4 input-select">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <i class="bi bi-check2-circle left-icon"></i>

                        <select class="form-control" name="status" required>
                            <option value="1" {{ $location->status == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ $location->status == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                </div>

                <div class="crm-action-bar mt-4">
                    <button type="button" onclick="history.back()" class="btn btn-outline-secondary">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-primary">Update Location</button>
                </div>

            </form>
        </div>

    </div>
</div>

@include('includes.footer_links')

</body>
</html>
