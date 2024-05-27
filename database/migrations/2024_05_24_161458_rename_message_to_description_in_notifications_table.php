<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            //$table->renameColumn('message','description');
            DB::statement("ALTER TABLE notifications CHANGE COLUMN message description TEXT");

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            //$table->renameColumn('description','message');
            DB::statement("ALTER TABLE notifications CHANGE COLUMN description message TEXT");
        });
    }
};
