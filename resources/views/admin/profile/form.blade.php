@extends('admin.layouts.common')
@section('content')
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">My Profile/</span> Edit</h4>
    @if (session('success'))
        <div id="success-alert" class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Basic Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.update.profile') }}" method="post">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="basic-default-fullname">Full Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}"
                                id="basic-default-fullname" placeholder="Enter Name" />
                            @error('name')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="basic-default-email">Email</label>
                            <div class="input-group input-group-merge">
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" id="basic-default-email"
                                    class="form-control @error('email') is-invalid @enderror" placeholder="Enter Email" aria-label="john.doe"
                                    aria-describedby="basic-default-email2" />
                            </div>
                            @error('email')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Change Password</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.update.profile') }}" method="post">
                        @csrf
                        <div class="mb-3 form-password-toggle">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="password">Current Password</label>
                            </div>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" name="password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="password" />
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                            @if ($errors->has('password'))
                                <div class="invalid-feedback d-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </div>
                            @endif
                        </div>
                        <div class="mb-3 form-password-toggle">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="new_password">New Password</label>
                            </div>
                            <div class="input-group input-group-merge">
                                <input type="password" id="new_password" class="form-control {{ $errors->has('new_password') ? 'is-invalid' : '' }}" name="new_password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="new_password" />
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                            @if ($errors->has('new_password'))
                                <div class="invalid-feedback d-block">
                                    <strong>{{ $errors->first('new_password') }}</strong>
                                </div>
                            @endif
                        </div>
                        <div class="mb-3 form-password-toggle">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="confirm_password">Confirm Password</label>
                            </div>
                            <div class="input-group input-group-merge">
                                <input type="password" id="confirm_password" class="form-control {{ $errors->has('confirm_password') ? 'is-invalid' : '' }}" name="confirm_password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="confirm_password" />
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                            @if ($errors->has('confirm_password'))
                                <div class="invalid-feedback d-block">
                                    <strong>{{ $errors->first('confirm_password') }}</strong>
                                </div>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
