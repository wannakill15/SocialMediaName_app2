@extends('layouts.app')
@section('title', 'Profile')


@section('content')
    <div class="container">
        <div class="row my-5">
            <div class="col-lg-12">
                <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="text-secondary fw-bold">User Profile</h2>
                        <a href="{{ route('auth.logout')}}" class="btn btn-dark">Logout</a>
                    </div>
                    <div class="card-body p-5">
                        <div class="row">
                            <div class="col-lg-4 px-5 text-center" style="border-right: 1px solid #999;">
                                <img src="{{ asset('img/profile.jpg')}}" id="image-preview" class="img-fluid rounded-circle img-thumbnail" width="200">
                                <div>
                                    <label for="picture">Change Profile Picture</label>
                                    <input type="file" name="picture" id="picture" class="form-group">
                                </div>
                            </div>
                            <div class="col-lg-8 px-5">
                                <form action="#" method="POST" id="profile_form">
                                    @csrf
                                    <div class="my-2">
                                        <label for="name">Full Name</label>
                                        <input type="text" name="name" id="name" class="form-control rounded-0"
                                        value="{{ $userInfo->name}}">
                                    </div>

                                    <div class="my-2">
                                        <label for="email">E-mail</label>
                                        <input type="email" name="email" id="email" class="form-control rounded-0"
                                        value="{{ $userInfo->email}}">
                                    </div>

                                    <div class="row">
                                        <div class="col-lg">
                                            <label for="gender">Gender</label>
                                            <select name="gender" id="gender" class="form-select rounded-0">
                                                <option value="" selected disabled>-Select-</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                            </select>
                                        </div>

                                        <div class="col-lg">
                                            <label for="dob">Date of Birth</label>
                                            <input type="date" name="dob" id="dob" class="form-control rounded-0"
                                            value="{{ $userInfo->dob}}">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')

@endsection