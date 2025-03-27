<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Remove auto-increment from id
        DB::statement('ALTER TABLE data_ibu_hamil MODIFY id BIGINT UNSIGNED NOT NULL;');
        
        // Drop primary key
        DB::statement('ALTER TABLE data_ibu_hamil DROP PRIMARY KEY;');
        
        // Add primary key on id_hamil
        DB::statement('ALTER TABLE data_ibu_hamil ADD PRIMARY KEY (id_hamil);');
        
        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Drop primary key
        DB::statement('ALTER TABLE data_ibu_hamil DROP PRIMARY KEY;');
        
        // Make id auto-increment again
        DB::statement('ALTER TABLE data_ibu_hamil MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY;');
        
        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
