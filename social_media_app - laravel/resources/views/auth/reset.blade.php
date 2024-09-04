@extends('layouts.app')
@Section('title', 'Reset Password')

@section('content')
<div class="container-fluid">
    <div class="row d-flex justify-content-center align-items-center min-vh-100">
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header">
                    <h2 class="fw-bold text-secondary">Reset Password</h2>
                </div>
                <div class="card-body p-5">
                    <div id ="reset_alert"></div>
                    <form action="#" method="POST" id="reset_form">
                        @csrf
                        <input type="hidden" name="email" value="{{$email}}">
                        <input type="hidden" name="token" value="{{$token}}">

                        <div class="mb-3">
                            <input type="email" name="email" id="email" class="form-control rounded-0" placeholder="E-mail" value="{{$email}}" disabled>
                            <div class="invalid-feedback"></div>                            
                        </div>

                        <div class="mb-3">
                            <input type="password" name="npass" id="npass" class="form-control rounded-0" placeholder="New Password">
                            <div class="invalid-feedback"></div>                            
                        </div>

                        <div class="mb-3">
                            <input type="password" name="cnpass" id="cnpass" class="form-control rounded-0" placeholder="Confirm Password">
                            <div class="invalid-feedback"></div>                            
                        </div>

                        <div class="mb-3 d-grid">
                            <input type="submit" value="Update Password" class="btn btn-dark rounded-0" id="reset_btn">
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script>
        $(function(){
            $("#reset_form").submit(function(e){
                e.preventDefault();
                $("#reset_btn").val('Please Wait... ');
                $.ajax({
                    url: '{{ route('auth.reset')}}',
                    method: 'post',
                    data: $(this).serialize(),
                    success: function(res){
                        if(res.status == 400){
                            showError('npass', res.messages.npass);
                            showError('cnpass', res.messages.cnpass);
                            $("#reset_btn").val('Update Password');
                        }else if(res.status == 401){
                            $("#reset_alert").html(showMessage('danger', res.messages));
                            removeValidationClasses("#reset_form");
                            $("#reset_btn").val('Update Password');
                        }else{
                            $("#reset_form")[0].reset();
                            $("#reset_alert").html(showMessage('success', res.messages));
                            $("#reset_btn").val('Update Password');
                        }
                    }
                });
            });
        });
    </script>
@endsection