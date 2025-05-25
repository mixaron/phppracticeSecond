<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description');
            $table->enum('status',
                ['new', 'in_progress', 'completed', 'cancelled', 'rejected'])->default('new');
            $table->string('phone')->nullable()->index();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId("service_id")->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
