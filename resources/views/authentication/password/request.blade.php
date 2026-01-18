@extends('layouts.authentication.master')
@section('title', 'Unlock')

@section('css')
@endsection

@section('style')
@endsection


@section('content')
<div class="tap-top"><i data-feather="chevrons-up"></i></div>
<!-- tap on tap ends-->
<!-- page-wrapper Start-->
<div class="page-wrapper">
   <div class="container-fluid p-0">
      <!-- Unlock page start-->
      <div class="authentication-main mt-0">
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
                        <form class="theme-form" action="{{route('password.email')}}" method="POST">
                           @csrf
                           <h4>Şifreyi sıfırla</h4>
                           @if ($message=session('status'))

                              <div class="alert alert-success">{{$message}}</div>

                           @endif
                           <div class="form-group">
                              <label class="col-form-label">Mail adresinizi girin</label>
                              <input class="form-control" type="email" name="email" required="">
                              <div class="show-hide"><span class="show"></span></div>
                           </div>
                           <div class="form-group mb-0">
                             
                              <button class="btn btn-primary btn-block" type="submit">Sıfırlama bağlantısı gönder</button>
                           </div>
                          
                        </form>
                     </div>
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