<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Perfas - 4Dimension">
    <meta name="keywords" content="Perfas DDO, BGYS, KVKK, ISO27001, ISO9001, ISO14001, ISO45001">
    <meta name="author" content="pixelstrap">
    <link rel="icon" href="{{asset('assets/images/perfas-dark.svg')}}" type="image/x-icon">
    <link rel="shortcut icon" href="{{asset('assets/images/perfas-dark.svg')}}" type="image/x-icon">
     <title>Perfas V2 - @yield('title')</title>
    <!-- Google font-->
    <link href="https://fonts.googleapis.com/css?family=Rubik:400,400i,500,500i,700,700i&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900&amp;display=swap" rel="stylesheet">
    
    @include('layouts.authentication.css')
    @yield('style') 
  </head>
  <body>
    <!-- login page start-->
    @yield('content')  
    <!-- latest jquery-->
    @include('layouts.authentication.script') 
  </body>
</html>