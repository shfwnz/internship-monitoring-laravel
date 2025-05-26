<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared('
            CREATE TRIGGER update_student_status_true
            AFTER INSERT ON internships
            FOR EACH ROW
            BEGIN
                UPDATE students
                SET status = true
                WHERE id = NEW.student_id;
            END
        ');

        DB::unprepared('
            CREATE TRIGGER update_student_status_false
            AFTER DELETE ON internships
            FOR EACH ROW
            BEGIN
                UPDATE students
                SET status = false
                WHERE id = OLD.student_id;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS update_student_status_true');
        DB::unprepared('DROP TRIGGER IF EXISTS update_student_status_false');
    }
};