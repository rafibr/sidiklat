<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PegawaiJpTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'pegawai_id',
        'tahun',
        'jp_target',
        'jp_tercapai',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'jp_target' => 'integer',
        'jp_tercapai' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }
}
