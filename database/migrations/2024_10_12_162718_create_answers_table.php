<?php

use App\Models\Question;
use App\Models\User;
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
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('question_id');
            $table->foreign('question_id')
                ->references('number_code')
                ->on('questions')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->float('year_1401')->default(0);
            $table->float('year_1402')->default(0);
            $table->float('grant_price')->default(0);
            $table->boolean('admin_approval')->default(0);
            $table->string('image_path_1401')->nullable();
            $table->string('image_path_1402')->nullable();
            $table->json('dissertation_1401')->nullable();//پایان نامه
            $table->json('dissertation_1402')->nullable();//پایان نامه
            $table->string('admin_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
