@extends('layouts.master')

@section('title', 'Bildirimler')

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Bildirimler</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tüm aktiviteleriniz ve uyarılarınız</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Bildirimler</span>
    </li>
@endsection

@section('content')
<div class="card">
    <div class="card-header border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-bell text-primary-500"></i>
                    Bildirim Listesi
                </h5>
            </div>
            @if(auth()->user()->unreadNotifications->count() > 0)
                <form action="{{ route('notifications.readAll') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-check-double mr-1"></i> Tümünü Okundu İşaretle
                    </button>
                </form>
            @endif
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($notifications as $notification)
                <div class="relative group p-4 sm:p-6 {{ $notification->read_at ? 'bg-white dark:bg-gray-800' : 'bg-blue-50/50 dark:bg-blue-900/10' }} hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                    <div class="flex items-start gap-4">
                        <!-- Icon -->
                        <div class="flex-shrink-0 mt-1">
                            @if($notification->type === 'App\Notifications\KeywordAlertNotification')
                                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-red-600 dark:text-red-400">
                                    <i class="fas fa-exclamation-triangle text-lg"></i>
                                </div>
                            @elseif($notification->type === 'App\Notifications\NewDocUploadedNotification')
                                <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                    <i class="fas fa-file-alt text-lg"></i>
                                </div>
                            @else
                                <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-bell text-lg"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $notification->data['message'] ?? 'Yeni Bildirim' }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                        @if(isset($notification->data['username']))
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $notification->data['username'] }}</span> 
                                        @endif
                                        @if(isset($notification->data['keyword']))
                                            tarafından tetiklenen anahtar kelime: <span class="font-medium text-red-600 dark:text-red-400">"{{ $notification->data['keyword'] }}"</span>
                                        @endif
                                        @if(isset($notification->data['process_name']))
                                            (Uygulama: {{ $notification->data['process_name'] }})
                                        @endif
                                    </p>
                                    <div class="mt-2 flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                        <div class="flex items-center gap-1">
                                            <i class="far fa-clock"></i>
                                            {{ $notification->created_at->diffForHumans() }}
                                        </div>
                                        @if(isset($notification->data['activity_id']))
                                            <a href="{{ route('activities.show', $notification->data['activity_id']) }}" class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 hover:underline">
                                                Detayları Gör
                                            </a>
                                        @endif
                                        @if(isset($notification->data['url']))
                                            <a href="{{ route('dokuman.download', ['path' => $notification->data['url']]) }}" class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 hover:underline">
                                                Dosyayı İndir
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Mark as read button (if unread) -->
                                @if(!$notification->read_at)
                                    <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors" title="Okundu olarak işaretle">
                                            <i class="fas fa-circle text-[10px] text-primary-500"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                        <i class="fas fa-check-circle text-3xl text-gray-400 dark:text-gray-500"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Her şey güncel!</h3>
                    <p class="mt-1 text-gray-500 dark:text-gray-400">Okunmamış bildiriminiz bulunmuyor.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $notifications->links() }}
</div>
@endsection
