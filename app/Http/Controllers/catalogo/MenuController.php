<?php

namespace App\Http\Controllers\catalogo;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Muestra la lista de menús en la vista index.
     */
    public function index()
    {
        $menus = Menu::orderBy('orden', 'asc')->get();

        return view('catalogo.menu.index', compact('menus'));
    }

    /**
     * Guarda un nuevo menú en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'menu' => 'required|string|max:255',
            'icono' => 'nullable|string|max:255',
            'orden' => 'required|integer|min:0',
        ]);

        Menu::create([
            'menu' => $validated['menu'],
            'icono' => $validated['icono'] ?? null,
            'orden' => $validated['orden'],
            'activo' => $request->has('activo'),
        ]);

        return redirect()->route('catalogo.menu.index')
            ->with('success', 'Menú creado correctamente.');
    }

    /**
     * Actualiza el menú especificado en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'menu' => 'required|string|max:255',
            'icono' => 'nullable|string|max:255',
            'orden' => 'required|integer|min:0',
        ]);

        $menu = Menu::findOrFail($id);
        $menu->update([
            'menu' => $validated['menu'],
            'icono' => $validated['icono'] ?? null,
            'orden' => $validated['orden'],
            'activo' => $request->has('activo'),
        ]);

        return redirect()->route('catalogo.menu.index')
            ->with('success', 'Menú actualizado correctamente.');
    }

    /**
     * Elimina el menú de la base de datos.
     */
    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();

        return redirect()->route('catalogo.menu.index')
            ->with('success', 'Menú eliminado correctamente.');
    }
}
