<?php

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('v2ray_configs', function (Blueprint $table) {
            $table->id();
            $table->string("remark")->unique();
            $table->unsignedInteger("size")->nullable();
            $table->unsignedInteger("days");
            $table->unsignedBigInteger("price");
            $table->timestamp("expire_at")->nullable();
            $table->timestamp("enabled_at")->nullable();
            $table->foreignIdFor(Admin::class);
            $table->foreignIdFor(User::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('v2ray_configs');
    }
};
