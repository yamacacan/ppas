@extends('layouts.authentication.master')
@section('title', 'Sign-up-one')

@section('css')
@endsection

@section('style')
@endsection

@section('content')
<div class="container-fluid p-0">
   <div class="row m-0">
      <div class="col-12 p-0">
         <div class="login-card">
            <div>
               <div class="text-center mb-4">
                  <a class="logo" href="{{ route('/') }}">
                     <img class="img-fluid" src="{{asset('assets/images/logo/logo.png')}}" alt="Perfas" style="max-height: 80px;">
                  </a>
               </div>
               <div class="login-main">
                  <form action="{{route('register')}}" method="POST" class="theme-form">
                     @csrf
                     <h4>Hesabınızı oluşturun</h4>
                     <p>Kayıt oluşturmak için lütfen bilgileri doldurun</p>
                     <div class="form-group">
                        <div class="row g-2">
                           <div class="col-6">
                              <label class="col-form-label pt-0">Adınız</label>
                              <input class="form-control @error('firstName') is-invalid @enderror" type="text" name="firstName" value="{{old('firstName')}}">
                              @error('firstName')
                              <div class="invalid-feedback">{{$message}}</div>
                              @enderror
                           </div>
                           <div class="col-6">
                              <label class="col-form-label pt-0">Soyadınız</label>
                              <input class="form-control @error('lastName') is-invalid @enderror" type="text" name="lastName" value="{{old('lastName')}}">
                              @error('lastName')
                              <div class="invalid-feedback">{{$message}}</div>
                              @enderror
                           </div>
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-form-label">E-Mail</label>
                        <input class="form-control @error('email') is-invalid @enderror" type="email"   name="email" value="{{old('email')}}" placeholder="ornek@email.com">
                              @error('email')
                              <div class="invalid-feedback">{{$message}}</div>
                              @enderror
                     </div>
                     <div class="form-group">
                        <label class="col-form-label">Şifre</label>
                        <input class="form-control @error('password') is-invalid @enderror" type="password" name="password" value="{{old('password')}}" placeholder="*********">
                              @error('password')
                              <div class="invalid-feedback">{{$message}}</div>
                              @enderror
                        <div class="show-hide"><span class="show"></span></div>
                     </div>
                     <div class="form-group">
                        <label class="col-form-label">Şifre tekrar </label>
                        <input class="form-control @error('password_confirmation') is-invalid @enderror" type="password" name="password_confirmation" value="{{old('password_confirmation')}}" placeholder="*********">
                              @error('password_confirmation')
                              <div class="invalid-feedback">{{$message}}</div>
                              @enderror
                        <div class="show-hide"><span class="show"></span></div>
                     </div>
                     <div class="form-group mb-0">
                        <div class="checkbox p-0">
                           <input id="checkbox1" type="checkbox">
                           <label class="text-muted" for="checkbox1">Kullanım şartlarını kabul ediyorum</label>
                        </div>
                        <button class="btn btn-primary btn-block" type="submit">Hesap Oluştur</button>
                     </div>
                     <p class="mt-4 mb-0">Zaten bir hesabım var?<a class="ms-2" href="{{ route('login') }}">Giriş Yap</a></p>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection

@section('script')
@endsection