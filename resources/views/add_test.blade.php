<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add Parameter/Unit - Rich Labz CRM</title>

    @include('includes.header_links')

    <!-- Tom Select -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

    <style>
        .input-icon input,
        .input-icon textarea {
            padding-left: 38px !important;
        }

        .input-icon {
            position: relative;
        }

        .input-icon>i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.13rem;
            color: #7a8599;
            pointer-events: none;
            z-index: 11;
        }

        .ts-control {
            min-height: 38px;
            border-radius: 8px !important;
            border-color: #ced4da !important;
            padding: 6px !important;
            font-size: 14px;
            background: #f9f9fb;
        }

        .ts-control .item {
            background: #0066cc !important;
            color: #fff !important;
            border-radius: 4px;
            padding: 3px 7px;
            font-size: 13px;
        }

        .ts-dropdown {
            border-radius: 8px !important;
        }
    </style>

</head>

<body>

    @include('includes.sidebar')
    @include('includes.header')

    <div class="content">
        <div class="container-crm">

            <div class="crm-header">
                <div class="crm-title">Add Parameter</div>

                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-back d-flex align-items-center gap-1" onclick="history.back()">
                        <i class="bi bi-arrow-left"></i> Back
                    </button>

                    <a href="{{ route('manage-tests') }}" class="create-invoice-btn">
                        Manage Parameter's
                    </a>
                </div>
            </div>

            {{-- SUCCESS MESSAGE --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    <strong><i class="bi bi-check-circle-fill"></i></strong>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- ERROR MESSAGE --}}
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    <strong><i class="bi bi-exclamation-triangle-fill"></i></strong>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- VALIDATION ERRORS --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    <strong><i class="bi bi-exclamation-circle-fill"></i> Validation Error:</strong>
                    {{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="crm-card">
                <form id="addPackageForm" action="{{ route('store-test') }}" method="POST" autocomplete="off">
                    @csrf

                    <div class="row mb-4">

                        {{-- TEST NAME --}}
                        <div class="col-md-4">
                            <label class="form-label">Parameter Name <span class="text-danger">*</span></label>
                            <div class="input-icon">
                                <i class="bi bi-box-seam"></i>
                                <input type="text" class="form-control" name="test_name" placeholder="Enter Parameter Name">
                            </div>
                            @error('test_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- SELECT UNIT --}}
                        <div class="col-md-4">
                            <label class="form-label">Select Units</label>
                            <select id="test_list" name="unit_id" placeholder="Search & Select Units..." autocomplete="off">
                                <option value="">Select Unit</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                @endforeach
                            </select>
                            @error('unit_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- NORMAL RANGE --}}
                        <div class="col-md-4">
                            <label class="form-label">Normal Range</label>
                            <div class="input-icon">
                                <i class="bi bi-list-check"></i>
                                <input type="text" class="form-control" name="normal_range" placeholder="e.g. 70 - 110 mg/dL">
                            </div>
                            @error('normal_range')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Price</label>
                            <div class="input-icon">
                               <i class="bi-cash"></i>
                                <input type="text" class="form-control" name="price" placeholder=" e.g. 99">
                            </div>
                            @error('price')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="crm-action-bar mt-4">
                        <button type="button" class="btn btn-outline-secondary" onclick="history.back()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>

                </form>
            </div>

        </div>
    </div>

    @include('includes.footer_links')

    <script>
        new TomSelect("#test_list", {
            plugins: { remove_button: { title: "Remove" }},
            maxItems: 1,
            searchField: ["text"],
            placeholder: "Search & select units...",
            persist: false,
            create: false
        });
    </script>

</body>

</html>
