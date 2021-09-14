<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $clinicId
 * @property int $physicianId
 * @property int $patientId
 * @property string $text
 */
class Prescription extends Model
{
    public $timestamps = false;
    protected $table = 'prescriptions';
    protected $dateFormat = 'U';
}
