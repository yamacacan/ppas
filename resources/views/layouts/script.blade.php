
 <!-- latest jquery-->
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
 <!-- Bootstrap js-->
<script src="{{asset('assets/js/bootstrap/bootstrap.bundle.min.js')}}"></script>
<!-- feather icon js-->
<script src="{{asset('assets/js/icons/feather-icon/feather.min.js')}}"></script>
<script src="{{asset('assets/js/icons/feather-icon/feather-icon.js')}}"></script>
<!-- scrollbar js-->
<script src="{{asset('assets/js/scrollbar/simplebar.js')}}"></script>
<script src="{{asset('assets/js/scrollbar/custom.js')}}"></script>
<!-- Sidebar jquery-->
<script src="{{asset('assets/js/config.js')}}"></script>
<!-- Plugins JS start-->
<script src="{{ asset('assets/js/chart/apex-chart/apex-chart.js') }}"></script>
<script src="{{ asset('assets/js/chart/apex-chart/stock-prices.js') }}"></script>
<script id="menu" src="{{asset('assets/js/sidebar-menu.js')}}"></script>
<script src="{{ asset('assets/js/slick/slick.min.js') }}"></script>
<script src="{{ asset('assets/js/slick/slick.js') }}"></script>
<script src="{{ asset('assets/js/header-slick.js') }}"></script>
 <script>
     function check_read(id)
     {
        $.ajax({
            'type':'POST',
            'url':'/notify/checkread',
            'data':'id='+id+'&_token={{csrf_token()}}',
            'success':function(e){
               $('#notification_li').html(e);
            }
        });
     }
 </script>
@yield('script')

{{-- @if(Route::current()->getName() != 'popover') 
	<script src="{{asset('assets/js/tooltip-init.js')}}"></script>
@endif --}}

<!-- Plugins JS Ends-->
<!-- Theme js-->
<script src="{{asset('assets/js/script.js')}}"></script>
{{-- <script src="{{asset('assets/js/theme-customizer/customizer.js')}}"></script> --}}


{{-- @if(Route::current()->getName() == 'index') 
	<script src="{{asset('assets/js/layout-change.js')}}"></script>
@endif --}}

@if(Route::currentRouteName() == 'index')
<script>
	new WOW().init();
</script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toast Configuration
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
            color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#545454',
            customClass: {
                popup: 'colored-toast dark:!bg-gray-800 dark:!text-white',
                title: 'dark:!text-white'
            },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Handle Session Messages
        @if(session('success'))
            Toast.fire({ icon: 'success', title: "{{ session('success') }}" });
        @endif

        @if(session('message'))
            Toast.fire({ icon: 'success', title: "{{ session('message') }}" });
        @endif

        @if(session('error'))
            Toast.fire({ icon: 'error', title: "{{ session('error') }}" });
        @endif

        @if(session('warning'))
            Toast.fire({ icon: 'warning', title: "{{ session('warning') }}" });
        @endif

        @if(session('info'))
            Toast.fire({ icon: 'info', title: "{{ session('info') }}" });
        @endif

        // Global Delete Confirmation
        document.body.addEventListener('submit', function(e) {
            if (e.target.classList.contains('delete-form')) {
                e.preventDefault();
                const form = e.target;
                
                Swal.fire({
                    title: 'Emin misiniz?',
                    text: "Bu işlem geri alınamaz!",
                    icon: 'warning',
                    heightAuto: false, // Fix layout shift
                    background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
                    color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#545454',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#3b82f6',
                    confirmButtonText: 'Evet, Sil!',
                    cancelButtonText: 'İptal',
                    customClass: {
                        popup: 'dark:bg-gray-800 dark:text-white',
                        title: 'dark:text-white',
                        htmlContainer: 'dark:text-gray-300'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }
        });
    });
</script>
