<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Test - Rich Labz CRM</title>

    @include('includes.header_links')

    <!-- Tom Select -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

    <style>
        .input-icon input {
            padding-left: 38px !important;
        }

        .input-select {
            position: relative;
        }

        .input-select select {
            padding-left: 38px !important;
            padding-right: 35px !important;
            background: #f9f9fb;
            appearance: none;
        }

        .dropdown-icon {
            position: absolute;
            right: 12px;
            top: 70%;
            transform: translateY(-50%);
            pointer-events: none;
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
                <div class="crm-title">Edit Test</div>

                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-back d-flex align-items-center gap-1" onclick="history.back()">
                        <i class="bi bi-arrow-left"></i> Back
                    </button>

                    <a href="{{ route('manage-tests') }}" class="create-invoice-btn">
                        Manage Tests
                    </a>
                </div>
            </div>

            <div class="crm-card">
                <form action="{{ route('update-test', $test->id) }}" method="POST">
                    @csrf

                    <div class="row mb-4">

                        <div class="col-md-4 input-icon">
                            <label class="form-label">Test Name <span class="text-danger">*</span></label>
                            <i class="bi bi-box-seam"></i>
                            <input type="text" class="form-control" name="test_name"
                                value="{{ old('test_name', $test->test_name) }}" required>
                            @error('test_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Select Units</label>
                            <select id="test_list" name="unit_id">
                                <option value="">Select Unit</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ $unit->id == $test->unit_id ? 'selected' : '' }}>
                                        {{ $unit->unit_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('unit_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 input-icon">
                            <label class="form-label">Normal Range</label>
                            <i class="bi bi-list-check"></i>
                            <input type="text" class="form-control" name="normal_range"
                                value="{{ old('normal_range', $test->normal_range) }}">
                            @error('normal_range')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        <div class="col-md-4 input-icon">
                            <label class="form-label">Price</label>
                            <i class="bi bi-cash"></i>
                            <input type="text" class="form-control" name="price"
                                value="{{ old('price', $test->price) }}">
                            @error('price')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- <div class="col-md-4 mt-3 input-icon">
                        <label class="form-label">Sample Type</label>
                        <i class="bi bi-droplet"></i>
                        <input type="text" class="form-control" name="sample_type"
                               value="{{ old('sample_type', $test->sample_type) }}">
                        @error('sample_type')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-4 mt-3 input-select">
                        <label class="form-label">Status</label>
                        <i class="bi bi-check-circle left-icon"></i>
                        <select name="status" class="form-control">
                            <option value="1" {{ $test->status == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ $test->status == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                        <i class="bi bi-chevron-down dropdown-icon"></i>
                        @error('status')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div> -->

                    </div>

                    <div class="crm-action-bar mt-4">
                        <a href="{{ route('manage-tests') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Test</button>
                    </div>

                </form>
            </div>

        </div>
    </div>

    @include('includes.footer_links')

    <script>
        new TomSelect("#test_list", {
            plugins: { remove_button: { title: "Remove" } },
            maxItems: 1,
            searchField: ["text"],
            persist: false,
            create: false
        });
    </script>

</body>

</html>