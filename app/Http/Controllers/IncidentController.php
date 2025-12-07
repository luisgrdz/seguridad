<?php

namespace App\Http\Controllers;

use App\Models\Camera;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncidentController extends Controller
{
    /**
     * Muestra el listado de incidencias (Bandeja de entrada)
     */
    public function index()
    {
        $user = Auth::user();
        $query = Incident::with(['camera', 'user']);

        // Filtros por rol
        if ($user->role->name === 'admin') {
            // Admin ve todo
        } elseif ($user->role->name === 'supervisor') {
            // Supervisor ve las de su equipo
            $teamIds = $user->subordinates()->pluck('id')->push($user->id);
            $query->whereHas('camera', function ($q) use ($teamIds) {
                $q->whereIn('user_id', $teamIds);
            });
        } else {
            // User normal ve solo las suyas
            $query->where('user_id', $user->id);
        }

        $incidents = $query->orderByRaw("FIELD(priority, 'critica', 'alta', 'media', 'baja')")
            ->latest()
            ->paginate(10);

        return view('incidents.index', compact('incidents'));
    }

    /**
     * Muestra el formulario para crear un reporte.
     * ESTA ES LA FUNCIÓN QUE TE FALTABA.
     */
    public function create(Camera $camera)
    {
        return view('incidents.create', compact('camera'));
    }

    /**
     * Guarda la incidencia en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'camera_id' => 'required|exists:cameras,id',
            'type' => 'required|string',
            'priority' => 'required|in:baja,media,alta,critica',
            'description' => 'required|string|max:1000',
        ]);

        Incident::create([
            'camera_id' => $validated['camera_id'],
            'user_id' => auth()->id(),
            'type' => $validated['type'],
            'priority' => $validated['priority'],
            'description' => $validated['description'],
            'status' => 'pendiente'
        ]);

        // CAMBIO IMPORTANTE: Usamos back() para recargar la misma página del monitor
        return back()->with('success', 'Reporte registrado correctamente.');
    }

    /**
     * Actualiza el estado de la incidencia (Para Admin/Supervisor).
     */
    public function update(Request $request, Incident $incident)
    {
        if (!in_array(Auth::user()->role->name, ['admin', 'supervisor'])) {
            abort(403, 'No tienes permisos para cambiar el estado.');
        }

        $request->validate([
            'status' => 'required|in:pendiente,en_revision,resuelto,cerrado'
        ]);

        $incident->update(['status' => $request->status]);

        return back()->with('success', 'Estado actualizado.');
    }
}
