<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrescriptionsTable extends Migration
{
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->integer('clinicId');
            $table->integer('physicianId');
            $table->integer('patientId');
            $table->text('text');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
}
