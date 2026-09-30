<?php

namespace App\Http\Controllers\catalogo;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\SubMenu;
use Illuminate\Http\Request;

class SubMenuController extends Controller
{
    /**
     * Muestra la lista de submenús y menús en la vista index.
     */
    public function index()
    {
        $subMenus = SubMenu::with('menu')->orderBy('menu_id', 'asc')->orderBy('orden', 'asc')->get();
        $menus = Menu::orderBy('orden', 'asc')->get();

        return view('catalogo.sub_menu.index', compact('subMenus', 'menus'));
    }

    /**
     * Guarda un nuevo submenú en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'menu_id'  => 'required|exists:menus,id',
            'sub_menu' => 'required|string|max:255',
            'url'      => 'nullable|string|max:255',
            'icono'    => 'nullable|string|max:255',
            'orden'    => 'required|integer|min:0',
        ]);

        SubMenu::create([
            'menu_id'  => $validated['menu_id'],
            'sub_menu' => $validated['sub_menu'],
            'url'      => $validated['url'] ?? null,
            'icono'    => $validated['icono'] ?? null,
            'orden'    => $validated['orden'] ?? 0,
            'activo'   => $request->has('activo'),
        ]);

        return redirect()->route('catalogo.sub_menu.index')
            ->with('success', 'Submenú registrado correctamente.');
    }

    /**
     * Actualiza el submenú especificado en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'menu_id'  => 'required|exists:menus,id',
            'sub_menu' => 'required|string|max:255',
            'url'      => 'nullable|string|max:255',
            'icono'    => 'nullable|string|max:255',
            'orden'    => 'required|integer|min:0',
        ]);

        $subMenu = SubMenu::findOrFail($id);
        $subMenu->update([
            'menu_id'  => $validated['menu_id'],
            'sub_menu' => $validated['sub_menu'],
            'url'      => $validated['url'] ?? null,
            'icono'    => $validated['icono'] ?? null,
            'orden'    => $validated['orden'] ?? 0,
            'activo'   => $request->has('activo'),
        ]);

        return redirect()->route('catalogo.sub_menu.index')
            ->with('success', 'Submenú actualizado correctamente.');
    }

    /**
     * Elimina el submenú de la base de datos.
     */
    public function destroy($id)
    {
        $subMenu = SubMenu::findOrFail($id);
        $subMenu->delete();

        return redirect()->route('catalogo.sub_menu.index')
            ->with('success', 'Submenú eliminado correctamente.');
    }
}
