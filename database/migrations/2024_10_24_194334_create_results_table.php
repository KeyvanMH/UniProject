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
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            //price before
            $table->float('sum_price')->default(0);
            //childnumber grant +0.1 for everychild
            $table->float('child_number')->default(0);
            //marriedwoman
            $table->float('is_married_woman')->default(0);
            $table->float('total_grant_price')->default(0);
//            $table->boolean('is_finished')->default(0);
            $table->timestamps();
        });
//        Schema::create('results',function (Blueprint $table){
//            $table->boolean('is_finished');
//        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
