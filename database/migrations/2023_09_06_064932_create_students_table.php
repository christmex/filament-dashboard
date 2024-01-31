<?php

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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nis')->nullable();
            $table->string('nisn')->nullable();
            $table->string('born_place')->nullable();
            $table->date('born_date')->nullable();
            $table->tinyInteger('sex')->nullable();
            $table->tinyInteger('religion')->nullable();
            $table->string('status_in_family')->nullable();
            $table->tinyInteger('sibling_order_in_family')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('previous_education')->nullable();
            $table->date('joined_at')->nullable();
            $table->string('joined_at_class')->nullable();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('parent_address')->nullable();
            $table->string('parent_phone')->nullable();
            $table->string('father_job')->nullable();
            $table->string('mother_job')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->string('guardian_address')->nullable();
            $table->string('guardian_job')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
