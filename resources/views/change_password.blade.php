<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Change Password - Rich Labz CRM</title>

    @include('includes.header_links')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" />
</head>

<body>

@include('includes.sidebar')
@include('includes.header')

<div class="content">
    <div class="container-crm">

        <!-- Page Header -->
        <div class="crm-header mb-4">
            <div class="d-flex align-items-center gap-3">
                <a href="javascript:history.back()" class="btn btn-light border d-flex align-items-center gap-1 px-3 py-2 shadow-sm">
                    <i class="bi bi-chevron-left"></i> Back
                </a>
                <div class="crm-title mb-0">Change Password</div>
            </div>
        </div>

        <!-- SUCCESS MESSAGE -->
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- ERROR MESSAGE -->
        @if ($errors->any())
            <div class="alert alert-danger">
                Please fix the errors below.
            </div>
        @endif

        <!-- Change Password Form -->
        <div class="crm-card">
            <form action="{{ route('password.update') }}" method="POST">
                @csrf

                <!-- Current Password -->
                <div class="mb-4">
                    <div class="crm-section-head">Current Password</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="input-icon">
                                <i class="bi bi-shield-lock" style="top:55%"></i>
                                <input type="password" name="current_password" class="form-control"
                                       placeholder="Enter current password">
                            </div>
                            @error('current_password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- New Password -->
                <div class="mb-4">
                    <div class="crm-section-head">New Password</div>
                    <div class="row g-3">

                        <div class="col-md-4">
                            <div class="input-icon">
                                <i class="bi bi-shield-lock" style="top:55%"></i>
                                <input type="password" name="new_password" class="form-control"
                                       placeholder="New password">
                            </div>
                            @error('new_password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <div class="input-icon">
                                <i class="bi bi-shield-lock" style="top:55%"></i>
                                <input type="password" name="confirm_password" class="form-control"
                                       placeholder="Confirm password">
                            </div>
                            @error('confirm_password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                    </div>

                    <div class="form-text text-muted mt-2">
                        Must be at least 8 characters and include uppercase, lowercase, numbers and symbols.
                    </div>

                </div>

                <!-- Buttons -->
                <div class="crm-action-bar">
                    <button type="button" class="btn btn-outline-secondary" onclick="history.back()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </div>

            </form>
        </div>

    </div>
</div>

@include('includes.footer_links')

</body>
</html>
