<?php

namespace App\Http\Controllers;

use App\Models\Encomienda;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EtiquetaController extends Controller
{
    public function show(int $id)
    {
        $encomienda = Encomienda::with(['emisor', 'receptor', 'operador'])->findOrFail($id);

        try {
            $qrCode = QrCode::size(130)->generate($encomienda->tracking_id);
        } catch (\Throwable $e) {
            $qrCode = null;
        }

        return view('encomiendas.etiqueta', [
            'encomienda' => $encomienda,
            'qrCode' => $qrCode,
        ]);
    }
}
