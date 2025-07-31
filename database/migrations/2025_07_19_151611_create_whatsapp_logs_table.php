<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable();
            $table->string('phone_number');
            $table->enum('message_type', ['text', 'document', 'image', 'template']);
            $table->text('message_content');
            $table->string('document_path')->nullable();
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed']);
            $table->text('response_data')->nullable();
            $table->text('error_message')->nullable();
            $table->string('gateway_message_id')->nullable();
            $table->string('whatsapp_message_id')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamps();
            
            $table->index(['phone_number', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('whatsapp_logs');
    }
};
