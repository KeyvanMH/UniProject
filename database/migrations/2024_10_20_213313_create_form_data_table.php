<?php

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
        Schema::create('form_data', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedTinyInteger('number_2_5_1401');
            $table->unsignedTinyInteger('number_2_5_1402');
            $table->unsignedTinyInteger('number_2_6_1401');
            $table->unsignedTinyInteger('number_2_6_1402');
            $table->unsignedTinyInteger('number_2_7_1401');
            $table->unsignedTinyInteger('number_2_7_1402');
            $table->unsignedTinyInteger('number_2_9_1_1401');
            $table->unsignedTinyInteger('number_2_9_1_1402');
            $table->unsignedTinyInteger('number_2_9_2_1401');
            $table->unsignedTinyInteger('number_2_9_2_1402');
            $table->unsignedTinyInteger('number_2_24_1401');
            $table->unsignedTinyInteger('number_2_24_1402');
            $table->unsignedTinyInteger('number_2_25_1401');
            $table->unsignedTinyInteger('number_2_25_1402');
            $table->unsignedTinyInteger('number_2_26_1401');
            $table->unsignedTinyInteger('number_2_26_1402');
            $table->unsignedTinyInteger('number_2_27_1401');
            $table->unsignedTinyInteger('number_2_27_1402');
            $table->unsignedTinyInteger('number_2_28_1401');
            $table->unsignedTinyInteger('number_2_28_1402');
            $table->unsignedTinyInteger('number_2_29_1401');
            $table->unsignedTinyInteger('number_2_29_1402');
            $table->unsignedTinyInteger('number_2_36_1401');
            $table->unsignedTinyInteger('number_2_36_1402');
            $table->unsignedTinyInteger('number_2_37_1401');
            $table->unsignedTinyInteger('number_2_37_1402');
            $table->unsignedTinyInteger('number_2_38_1401');
            $table->unsignedTinyInteger('number_2_38_1402');
            $table->unsignedTinyInteger('special_3_3_1');
            $table->unsignedTinyInteger('number_2_10_1_1401');
            $table->unsignedTinyInteger('number_2_10_1_1402');
            $table->unsignedTinyInteger('number_2_10_2_1401');
            $table->unsignedTinyInteger('number_2_10_2_1402');
            $table->unsignedTinyInteger('number_2_10_3_1401');
            $table->unsignedTinyInteger('number_2_10_3_1402');
            $table->unsignedTinyInteger('number_2_11_1_1401');
            $table->unsignedTinyInteger('number_2_11_1_1402');
            $table->unsignedTinyInteger('number_2_11_2_1401');
            $table->unsignedTinyInteger('number_2_11_2_1402');
            $table->unsignedTinyInteger('number_2_11_3_1401');
            $table->unsignedTinyInteger('number_2_11_3_1402');
            $table->unsignedTinyInteger('number_2_20_1401');
            $table->unsignedTinyInteger('number_2_20_1402');
            $table->unsignedTinyInteger('number_3_3_2_1401');
            $table->unsignedTinyInteger('number_3_3_2_1402');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_data');
    }
};
