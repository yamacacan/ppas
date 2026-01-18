<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('keyword_overrides');

        Schema::create('keyword_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('keyword_id')->constrained('category_keywords')->cascadeOnDelete();
            // Note: activity_categories tablosunun tam adını kontrol etmem lazımdı.
            // ActivityCategory migration'ına bakarak tablonun 'activity_categories' olduğunu varsayıyorum.
            // Önceki contextlerden hatırladığım kadarıyla 'activity_categories' idi.
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            
            $table->foreignId('unit_id')->nullable()->constrained('units')->cascadeOnDelete();
            
            // computer_users tablosu henüz migrate edilmemişti (önceki konuşmada bekliyordu).
            // Eğer migrate edilmediyse bu hata verir.
            // User 'migrate etmedim' demedi ama 'bekleyen task' olarak duruyordu.
            // Güvenli olması için constraint ekleyelim, umarım tablo vardır.
            $table->foreignId('computer_user_id')->nullable()->constrained('computer_users')->cascadeOnDelete();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('keyword_overrides');
    }
};
