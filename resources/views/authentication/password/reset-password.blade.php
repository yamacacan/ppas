@extends('layouts.authentication.master')
@section('title', 'Reset-password')

@section('css')
@endsection

@section('style')
@endsection


@section('content')
<!-- tap on top starts-->
<div class="tap-top"><i data-feather="chevrons-up"></i></div>
<!-- tap on tap ends-->
<!-- page-wrapper Start-->
<div class="page-wrapper">
   <div class="container-fluid p-0">
      <div class="row">
         <div class="col-12">
            <div class="login-card">
               <div>
                  <div class="text-center mb-4">
                     <a class="logo" href="{{ route('/') }}">
                        <img class="img-fluid" src="{{asset('assets/images/logo/logo.png')}}" alt="Perfas" style="max-height: 80px;">
                     </a>
                  </div>
                  <div class="login-main">
                     <form class="theme-form" action="{{route('password.update')}}" method="POST">
                        @csrf
                        <h4>Şifre Oluşturun</h4>
                        <div class="form-group">
                           <label class="col-form-label">Şifre</label>
                           <input class="form-control @error('password') is-invalid @enderror" type="password" name="password" value="{{old('password')}}">
                                 @error('password')
                                 <div class="invalid-feedback">{{$message}}</div>
                                 @enderror
                           <div class="show-hide"><span class="show"></span></div>
                        </div>
                        <div class="form-group">
                           <label class="col-form-label">Şifre tekrar </label>
                           <input class="form-control @error('password_confirmation') is-invalid @enderror" type="password" name="password_confirmation" value="{{old('password_confirmation')}}">
                                 @error('password_confirmation')
                                 <div class="invalid-feedback">{{$message}}</div>
                                 @enderror
                           <div class="show-hide"><span class="show"></span></div>

                           <input type="hidden" name="token" value="{{request()->route('token')}}">
                        </div>
                        <div class="form-group mb-0">
                           <button class="btn btn-primary btn-block" type="submit">Şifreyi Değiştir</button>
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