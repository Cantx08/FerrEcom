<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::withCount('producto')->orderBy('nombre')->paginate(15);
        return view('admin.categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('admin.categorias.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|max:100|unique:categorias,nombre',
            'descripcion' => 'nullable|string|max:255',
            'estado'      => 'boolean',
        ]);

        $validated['slug']   = $this->generarSlugUnico($validated['nombre']);
        $validated['estado'] = $request->has('estado');

        Categoria::create($validated);

        return redirect()->route('categorias.index')->with('success', 'Categoría creada exitosamente.');
    }

    public function edit(Categoria $categoria)
    {
        return view('admin.categorias.form', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $validated = $request->validate([
            'nombre'      => ['required', 'string', 'max:100', Rule::unique('categorias', 'nombre')->ignore($categoria->id)],
            'descripcion' => 'nullable|string|max:255',
            'estado'      => 'boolean',
        ]);

        if ($validated['nombre'] !== $categoria->nombre) {
            $validated['slug'] = $this->generarSlugUnico($validated['nombre'], $categoria->id);
        }

        $validated['estado'] = $request->has('estado');

        $categoria->update($validated);

        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada exitosamente.');
    }

    /**
     * La FK productos.categoria_id tiene ON DELETE CASCADE: borrar una categoría
     * con productos asociados los eliminaría a todos. Se bloquea para evitar
     * pérdida de datos accidental.
     */
    public function destroy(Categoria $categoria)
    {
        if ($categoria->producto()->exists()) {
            return redirect()
                ->route('categorias.index')
                ->with('error', 'No se puede eliminar: la categoría tiene productos asociados. Reasigna o elimina esos productos primero.');
        }

        $categoria->delete();

        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada exitosamente.');
    }

    private function generarSlugUnico(string $nombre, ?int $ignorarId = null): string
    {
        $slugBase = Str::slug($nombre);
        $slug     = $slugBase;
        $i        = 1;

        while (Categoria::where('slug', $slug)->when($ignorarId, fn ($q) => $q->where('id', '!=', $ignorarId))->exists()) {
            $slug = $slugBase . '-' . (++$i);
        }

        return $slug;
    }
}
