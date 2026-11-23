<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'apellido',
        'documento_tipo',
        'documento_numero',
        'telefono',
        'direccion',
        'correo_electronico',
    ];

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellido}";
    }

    public function getDocumentoCompletoAttribute(): string
    {
        return "{$this->documento_tipo}: {$this->documento_numero}";
    }

    public function encomiendasEnviadas(): HasMany
    {
        return $this->hasMany(Encomienda::class, 'cliente_emisor_id');
    }

    public function encomiendasRecibidas(): HasMany
    {
        return $this->hasMany(Encomienda::class, 'cliente_receptor_id');
    }
}
