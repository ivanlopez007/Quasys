<?php

namespace App\Http\Controllers\catalogo;

use App\Http\Controllers\Controller;
use App\Models\AccesoAsignado;
use App\Models\Menu;
use App\Models\Rol;
use Illuminate\Http\Request;

class AccesoAsignadoController extends Controller
{
    /**
     * Muestra los roles y la matriz de submenús agrupados por menú.
     */
    public function index(Request $request)
    {
        $roles = Rol::orderBy('rol', 'asc')->get();

        // Obtener el rol seleccionado (por parámetro GET o el primer rol existente)
        $selectedRolId = $request->get('rol_id', $roles->first()?->id);

        // Obtener la estructura completa de Menús y Submenús
        $menus = Menu::with(['subMenus' => function ($query) {
            $query->where('activo', true)->orderBy('orden', 'asc');
        }])->where('activo', true)->orderBy('orden', 'asc')->get();

        // Obtener los IDs de submenús actualmente asignados al rol seleccionado
        $assignedSubMenuIds = [];
        if ($selectedRolId) {
            $assignedSubMenuIds = AccesoAsignado::where('rol_id', $selectedRolId)
                ->pluck('sub_menu_id')
                ->toArray();
        }

        return view('catalogo.acceso_asignado.index', compact(
            'roles',
            'menus',
            'selectedRolId',
            'assignedSubMenuIds'
        ));
    }

    /**
     * Guarda / Actualiza la asignación masiva de submenús para el rol seleccionado.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rol_id'        => 'required|exists:rols,id',
            'sub_menus'     => 'nullable|array',
            'sub_menus.*'   => 'exists:sub_menus,id',
        ]);

        $rolId = $validated['rol_id'];
        $subMenusSeleccionados = $validated['sub_menus'] ?? [];

        // 1. Eliminar los accesos actuales del rol
        AccesoAsignado::where('rol_id', $rolId)->delete();

        // 2. Insertar los nuevos accesos seleccionados
        $nuevosAccesos = [];
        $now = now();

        foreach ($subMenusSeleccionados as $subMenuId) {
            $nuevosAccesos[] = [
                'rol_id'      => $rolId,
                'sub_menu_id' => $subMenuId,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }

        if (!empty($nuevosAccesos)) {
            AccesoAsignado::insert($nuevosAccesos);
        }

        return redirect()->route('catalogo.acceso_asignado.index', ['rol_id' => $rolId])
            ->with('success', 'Permisos y accesos actualizados correctamente.');
    }
}
