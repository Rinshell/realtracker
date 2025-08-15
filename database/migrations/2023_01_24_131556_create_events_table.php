<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;
use App\Models\Techno;
use App\Models\Status;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignIdFor(Product::class);
            $table->integer('sn_n');
            $table->integer('sn_m');
            $table->integer('sn_p');
            $table->string('description', 2047)->nullable()->default(null);
            $table->foreignIdFor(Techno::class);
            $table->foreignIdFor(Status::class);
            $table->boolean('active')->default(true);
            $table->foreignIdFor(User::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
};
