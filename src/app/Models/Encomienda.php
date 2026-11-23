<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Encomienda extends Model
{
    use HasFactory;

    protected $table = 'encomiendas';

    protected $fillable = [
        'tracking_id',
        'cliente_emisor_id',
        'cliente_receptor_id',
        'origen',
        'destino',
        'descripcion_paquete',
        'peso_kg',
        'dimensiones_largo',
        'dimensiones_ancho',
        'dimensiones_alto',
        'costo_servicio',
        'estado_actual',
        'operador_id',
        'repartidor_id',
    ];

    protected function casts(): array
    {
        return [
            'peso_kg' => 'decimal:2',
            'dimensiones_largo' => 'decimal:2',
            'dimensiones_ancho' => 'decimal:2',
            'dimensiones_alto' => 'decimal:2',
            'costo_servicio' => 'decimal:2',
        ];
    }

    public static function generateTrackingId(): string
    {
        $prefix = 'ENC-' . date('Ymd') . '-';
        $last = self::where('tracking_id', 'like', $prefix . '%')->latest('id')->first();
        if ($last) {
            $lastNumber = intval(substr($last->tracking_id, strlen($prefix)));
            $nextNumber = str_pad((string)($lastNumber + 1), 5, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '00001';
        }
        return $prefix . $nextNumber;
    }

    protected static function booted(): void
    {
        static::creating(function ($encomienda) {
            if (empty($encomienda->tracking_id)) {
                $encomienda->tracking_id = self::generateTrackingId();
            }
        });

        static::created(function ($encomienda) {
            HistorialEstado::create([
                'encomienda_id' => $encomienda->id,
                'estado_anterior' => null,
                'estado_nuevo' => $encomienda->estado_actual ?? 'recibido',
                'observaciones' => 'Admisión de encomienda registrada en el sistema',
                'user_id' => $encomienda->operador_id ?? auth()->id(),
                'timestamp' => now(),
            ]);
        });
    }

    public function emisor(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_emisor_id');
    }

    public function receptor(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_receptor_id');
    }

    public function operador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operador_id');
    }

    public function repartidor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'repartidor_id');
    }

    public function historialEstados(): HasMany
    {
        return $this->hasMany(HistorialEstado::class, 'encomienda_id')->orderBy('timestamp', 'desc');
    }
}
