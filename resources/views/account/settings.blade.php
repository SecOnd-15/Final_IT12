@extends('layouts.app')

@section('title', 'Account Settings - ATIN')

@push('styles')
<style>
    .settings-card {
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        border: var(--premium-border);
        background: #ffffff;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .settings-card:hover {
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background: rgba(249, 250, 251, 0.5) !important;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
        padding: 16px 24px !important;
        backdrop-filter: blur(8px);
    }

    .card-title {
        font-size: 0.95rem !important;
        font-weight: 600 !important;
        color: #374151 !important;
        letter-spacing: -0.01em;
    }

    .card-body {
        padding: 24px !important;
    }

    .form-label {
        font-weight: 500;
        font-size: 0.8rem;
        color: #6b7280;
        margin-bottom: 0.4rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .form-control, .form-select {
        border-radius: 8px;
        padding: 0.6rem 0.8rem;
        border: 1px solid #e5e7eb;
        font-size: 0.9rem;
        background-color: #f9fafb;
        transition: all 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
        background-color: #ffffff;
        border-color: var(--brand-primary);
        box-shadow: 0 0 0 4px rgba(6, 68, 138, 0.08);
        outline: none;
    }

    .btn {
        border-radius: 8px;
        padding: 0.6rem 1.4rem;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-primary {
        background-color: var(--brand-primary);
        color: #ffffff;
        border: none;
        box-shadow: 0 4px 12px rgba(6, 68, 138, 0.15);
    }

    .btn-primary:hover {
        background-color: #053b75;
        transform: translateY(-1px);
        box-shadow: 0 6px 15px rgba(6, 68, 138, 0.25);
    }

    .btn-success {
        background-color: #10b981;
        border: none;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
    }

    .btn-success:hover {
        background-color: #059669;
        transform: translateY(-1px);
        box-shadow: 0 6px 15px rgba(16, 185, 129, 0.25);
    }
</style>
@endpush

@section('content')
@include('components.alerts')

<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-5">
        <h2 class="fw-bold mb-1" style="color: var(--congress-blue); letter-spacing: -0.5px;">
            <i class="bi bi-person-gear me-2"></i>Account Settings
        </h2>
        <p class="text-secondary small mb-0">Manage your profile information and security settings.</p>
    </div>

    @if(!$user)
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle me-2"></i>
            User not found. Please <a href="{{ route('login') }}" class="alert-link">log in again</a>.
        </div>
    @else
    <div class="row">
        <!-- Personal Information -->
        <div class="col-lg-8">
            <div class="card settings-card mb-4">
                <div class="card-header d-flex align-items-center">
                    <i class="bi bi-person-vcard me-2 fs-5" style="color: #64748b;"></i>
                    <h5 class="card-title mb-0">Personal Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('account.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- First Name -->
                            <div class="col-md-6 mb-3">
                                <label for="f_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('f_name') is-invalid @enderror" 
                                       id="f_name" name="f_name" 
                                       value="{{ old('f_name', $user->f_name) }}" 
                                       placeholder="Enter first name" 
                                       maxlength="100" required>
                                @error('f_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Middle Name -->
                            <div class="col-md-6 mb-3">
                                <label for="m_name" class="form-label">Middle Name</label>
                                <input type="text" class="form-control @error('m_name') is-invalid @enderror" 
                                       id="m_name" name="m_name" 
                                       value="{{ old('m_name', $user->m_name) }}" 
                                       placeholder="Enter middle name" 
                                       maxlength="100">
                                @error('m_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Last Name -->
                            <div class="col-md-6 mb-3">
                                <label for="l_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('l_name') is-invalid @enderror" 
                                       id="l_name" name="l_name" 
                                       value="{{ old('l_name', $user->l_name) }}" 
                                       placeholder="Enter last name" 
                                       maxlength="100" required>
                                @error('l_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Contact Number -->
                            <div class="col-md-6 mb-3">
                                <label for="contactNo" class="form-label">Contact Number</label>
                                <input type="text" class="form-control @error('contactNo') is-invalid @enderror" 
                                       id="contactNo" name="contactNo" 
                                       value="{{ old('contactNo', $user->contactNo) }}" 
                                       placeholder="Enter contact number" 
                                       maxlength="11"
                                       pattern="[0-9]{0,11}"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)">
                                @error('contactNo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-12 mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" 
                                       value="{{ old('email', $user->email) }}" 
                                       placeholder="Enter email address" 
                                       maxlength="255" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i> Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Password Change -->
            <div class="card settings-card mb-4">
                <div class="card-header d-flex align-items-center">
                    <i class="bi bi-shield-check me-2 fs-5" style="color: #64748b;"></i>
                    <h5 class="card-title mb-0">Change Password</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('account.settings.password') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Current Password -->
                            <div class="col-12 mb-3">
                                <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" name="current_password" 
                                       placeholder="Enter current password" 
                                       required>
                                       <button class="btn btn-outline-secondary toggle-password bg-white" type="button">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- New Password -->
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">New Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" placeholder="Enter new password" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Minimum 8 characters</div>
                            </div>
                            

                            <!-- Confirm New Password -->
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" 
                                       placeholder="Confirm new password" 
                                       required>
                                <button class="btn btn-outline-secondary toggle-password bg-white" type="button">
                                    <i class="bi bi-eye"></i>
                                </button>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-key me-2"></i> Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

          
        </div>

        <!-- System Information & Admin-Managed Fields -->
        <div class="col-lg-4">
            <!-- System Information -->
            <div class="card settings-card mb-4">
                <div class="card-header d-flex align-items-center">
                    <i class="bi bi-info-circle me-2 fs-5" style="color: #64748b;"></i>
                    <h5 class="card-title mb-0">System Information</h5>
                </div>

                <div class="card-body">
                    <!-- System Info -->
                    <div class="list-group list-group-flush mb-4">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent border-bottom-0 py-2">
                            <span class="text-muted small text-uppercase fw-bold letter-spacing-05">Username:</span>
                            <span class="fw-semibold text-dark">{{ $user->username }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent border-bottom-0 py-2">
                            <span class="text-muted small text-uppercase fw-bold letter-spacing-05">Role:</span>
                            <span class="badge bg-light text-primary border border-primary border-opacity-10 py-1 px-3">{{ $user->role }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent border-bottom-0 py-2">
                            <span class="text-muted small text-uppercase fw-bold letter-spacing-05">Account Created:</span>
                            <span class="fw-semibold text-dark">{{ $user->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>

                    <!-- Admin Managed Note (Secondary Info) -->
                    <div class="admin-note d-flex align-items-center mt-2">
                        <i class="bi bi-wrench me-2 fs-6 text-warning"></i>
                        <span>Username & role only editable by administrators.</span>
                    </div>
                </div>
            </div>

              <!-- Session Timeout Settings -->
            <div class="card settings-card mb-4">
                <div class="card-header d-flex align-items-center">
                    <i class="bi bi-clock-history me-2 fs-5" style="color: #64748b;"></i>
                    <h5 class="card-title mb-0">Session Timeout Settings</h5>
                </div>
                <div class="card-body">
                    <form id="sessionForm" action="{{ route('session.timeout.update') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="session_timeout" class="form-label">Auto-logout after inactivity:</label>
                            <select name="timeout" id="session_timeout" class="form-select">
                                @php
                                    // Get current timeout from session (which comes from database on login)
                                    $currentTimeout = session('session_timeout', 600);
                                @endphp
                                
                                <option value="300" {{ $currentTimeout == 300 ? 'selected' : '' }}>5 minutes</option>
                                <option value="600" {{ $currentTimeout == 600 ? 'selected' : '' }}>10 minutes (default)</option>
                                <option value="1800" {{ $currentTimeout == 1800 ? 'selected' : '' }}>30 minutes</option>
                                <option value="3600" {{ $currentTimeout == 3600 ? 'selected' : '' }}>1 hour</option>
                                <option value="7200" {{ $currentTimeout == 7200 ? 'selected' : '' }}>2 hours</option>
                                <option value="14400" {{ $currentTimeout == 14400 ? 'selected' : '' }}>4 hours</option>
                                <option value="28800" {{ $currentTimeout == 28800 ? 'selected' : '' }}>8 hours</option>
                                <option value="0" {{ $currentTimeout == 0 ? 'selected' : '' }}>Never auto-logout</option>
                            </select>
                            
                            <div class="form-text mt-2">
                                <i class="bi bi-info-circle"></i>
                                Current setting: 
                                @if($currentTimeout == 0)
                                    <span class="text-success fw-bold">Never auto-logout</span>
                                @elseif($currentTimeout == 60)
                                    <span class="fw-bold">1 minute</span>
                                @else
                                    <span class="fw-bold">{{ round($currentTimeout / 60) }} minutes</span>
                                @endif
                                <br>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end mt-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i> Save Setting
                            </button>
                        </div>
                    </form>       
                </div>
            </div>

            @if(session('user_role') == 'Administrator')
            <!-- Backup -->
            <div class="card settings-card mb-4 shadow-sm border-0 bg-light bg-opacity-50">
                <div class="card-header d-flex align-items-center bg-transparent border-0">
                    <i class="bi bi-database me-2 fs-5 text-success"></i>
                    <h5 class="card-title mb-0">Database Backup</h5>
                </div>
                <div class="card-body pt-0">
                    <p class="text-muted small mb-4">Secure your data by creating a complete manual backup of the system database.</p>
                    <form action="{{ route('database.backup') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success w-100 py-2">
                            <i class="bi bi-download me-2"></i> Generate Backup
                        </button>
                    </form>
                </div>
            </div>
            @endif   
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Contact number validation
        const contactInput = document.getElementById('contactNo');
        if (contactInput) {
            contactInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);
            });
        }

        // Toggle Password Visibility
        const toggleButtons = document.querySelectorAll('.toggle-password');
        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Find the input field relative to this button
                const input = this.parentElement.querySelector('input');
                const icon = this.querySelector('i');
                
                if (input.type === "password") {
                    input.type = "text";
                    icon.classList.replace('bi-eye', 'bi-eye-slash');
                } else {
                    input.type = "password";
                    icon.classList.replace('bi-eye-slash', 'bi-eye');
                }
            });
        });

        // Password confirmation validation
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('password_confirmation');
        
        function validatePassword() {
            if (password && confirmPassword && password.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity("Passwords don't match");
            } else if (confirmPassword) {
                confirmPassword.setCustomValidity('');
            }
        }

        if (password && confirmPassword) {
            password.addEventListener('input', validatePassword);
            confirmPassword.addEventListener('input', validatePassword);
        }

        // Auto-hide alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
    });

    function confirmRestore() {
        return confirm(
            '⚠️ CRITICAL WARNING ⚠️\n\n' +
            '1. This will overwrite ALL current database data\n' +
            '2. ALL product images will be DELETED\n' +
            '3. This action cannot be undone\n\n' +
            'Are you absolutely sure you want to continue?'
        );
    }
</script>
@endpush