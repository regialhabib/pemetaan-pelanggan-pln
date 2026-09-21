@extends('layouts.app')
@section('main-content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        
    @endif

    @if ($errors->any())
        <div class="alert alert-danger border-left-danger" role="alert">
            <ul class="pl-4 my-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">

        <div class="col-lg-4 order-lg-2">

            <div class="card shadow mb-4">
                <div class="card-profile-image mt-4 text-center">
                    <img src="{{ asset('images/users/avatar-1.jpg') }}" class="rounded-circle"
                        style="height: 180px; width: 180px; object-fit: cover;" alt="Profile Image">
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <h5 class="font-weight-bold">{{ auth()->user()->nama }}</h5>
                            <p>{{ auth()->user()->role ?? 'User' }}</p>
                        </div>
                    </div>

                    {{-- <div class="row text-center">
                        <div class="col-md-4">
                            <span class="heading">22</span>
                            <span class="description">Friends</span>
                        </div>
                        <div class="col-md-4">
                            <span class="heading">10</span>
                            <span class="description">Photos</span>
                        </div>
                        <div class="col-md-4">
                            <span class="heading">89</span>
                            <span class="description">Comments</span>
                        </div>
                    </div> --}}
                </div>
            </div>


        </div>

        <div class="col-lg-8 order-lg-1">

            <div class="card shadow mb-4">

                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">My Account</h6>
                </div>

                <div class="card-body">

                    <form method="POST" autocomplete="off" action="{{ route('profile.update') }}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">



                        <h6 class="heading-small text-muted mb-4">User information</h6>

                        <div class="pl-lg-4">
                            <div class="row mb-3">
                                <div class="col-lg-6">
                                    <div class="form-group focused">
                                        <label class="form-control-label" for="name">Nama<span
                                                class="small text-danger">*</span></label>
                                        <input type="text" id="name" class="form-control" name="nama"
                                            value="{{ auth()->user()->nama }}" placeholder="Name">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-control-label" for="email">Email address<span
                                            class="small text-danger">*</span></label>
                                    <input type="email" id="email" class="form-control" name="email"
                                        placeholder="example@example.com" value="{{ auth()->user()->email }}">
                                </div>
                            </div>
                        </div>



                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group focused">
                                    <label class="form-control-label" for="current_password">Current password</label>
                                    <input type="password" id="current_password" class="form-control"
                                        name="current_password" placeholder="Current password">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group focused">
                                    <label class="form-control-label" for="new_password">New password</label>
                                    <input type="password" id="new_password" class="form-control" name="new_password"
                                        placeholder="New password">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group focused">
                                    <label class="form-control-label" for="confirm_password">Confirm password</label>
                                    <input type="password" id="confirm_password" class="form-control"
                                        name="password_confirmation" placeholder="Confirm password">
                                </div>
                            </div>
                        </div>
                </div>

                <!-- Button -->
                <div class="pl-lg-4 mb-3">
                    <div class="row">
                        <div class="col text-center">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </div>
                </div>
                </form>

            </div>

        </div>

    </div>

    </div>
@endsection
