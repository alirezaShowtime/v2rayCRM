<?php

use App\Models\Inbound;
use App\Models\V2rayConfig;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inbounds', function (Blueprint $table) {
            $table->id();
            $table->string("name")->unique();
            $table->string("type");
            $table->timestamps();
        });

        Schema::create("inbound_v2ray_config", function (Blueprint $table) {
            $table->foreignIdFor(V2rayConfig::class);
            $table->foreignIdFor(Inbound::class);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inbounds');
    }
};
