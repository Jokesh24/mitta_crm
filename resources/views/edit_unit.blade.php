<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Unit - Rich Labz CRM</title>

    @include('includes.header_links')
</head>

<body>

    @include('includes.sidebar')
    @include('includes.header')

    <div class="content">
        <div class="container-crm">

            <div class="crm-header">
                <div class="crm-title">Edit Unit</div>

                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-back d-flex align-items-center gap-1" onclick="history.back()">
                        <i class="bi bi-arrow-left"></i> Back
                    </button>

                    <a href="{{ route('manage-units') }}" class="create-invoice-btn">
                        Manage Units
                    </a>
                </div>
            </div>

            <div class="crm-card">
                <form action="{{ route('update-unit', $unit->id) }}" method="POST">
                    @csrf

                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label">Unit Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="unit_name"
                                value="{{ old('unit_name', $unit->unit_name) }}" required>
                            @error('unit_name')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-4 input-select">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <i class="bi bi-check-circle left-icon"></i>
                            <select class="form-control" name="status" required>
                                <option value="1" {{ $unit->status == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $unit->status == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                            <i class="bi bi-chevron-down dropdown-icon"></i>

                            @error('status')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    <div class="crm-action-bar mt-4">
                        <a href="{{ route('manage-units') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Unit</button>
                    </div>

                </form>
            </div>

        </div>
    </div>

    @include('includes.footer_links')

</body>

</html>
