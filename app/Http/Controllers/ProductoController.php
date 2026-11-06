<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Producto;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::with('categoria');
        
        $lowStockSuppliers = [];
        
        if ($request->has('low_stock') && $request->low_stock == 1) {
            // Filtrar productos con bajo stock
            $query->lowStock();

            // Obtener productos locales con bajo stock para agrupar proveedores
            $lowStockProducts = Producto::lowStock()->get();
            if ($lowStockProducts->count() > 0) {
                $names = $lowStockProducts->pluck('nombre')->toArray();
                
                // Buscar en el catálogo de proveedores
                $productoProveedores = \App\Models\ProductoProveedor::with('proveedor')
                    ->whereIn('nombre', $names)
                    ->get();
                    
                $lowStockSuppliers = $productoProveedores->groupBy('proveedor_id')->map(function($items) {
                    return [
                        'proveedor' => $items->first()->proveedor,
                        'productos_count' => $items->count(),
                        'proveedor_id' => $items->first()->proveedor_id
                    ];
                });
            }
        }
        
        $productos = $query->orderBy('nombre')->paginate(15);
        
        // Agregar low_stock a los links de paginación
        if ($request->has('low_stock')) {
            $productos->appends(['low_stock' => $request->low_stock]);
        }
        
        return view('admin.productos.index', compact('productos', 'lowStockSuppliers'));
    }

    public function create()
    {
        $categorias = \App\Models\Categoria::orderBy('nombre')->get();
        return view('admin.productos.form', compact('categorias'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:50|unique:productos',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'unidad_medida' => 'required|string|max:30',
            'precio_venta' => 'required|numeric|min:0',
            'precio_compra' => 'required|numeric|min:0',
            'stock_actual' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
            'estado' => 'boolean',
            'categoria_id' => 'required|exists:categorias,id',
            'imagen' => 'nullable|image|max:2048',
            'cantidad_por_mayor' => 'nullable|integer|min:1',
            'descuento_por_mayor' => 'nullable|numeric|min:0|max:100'
        ]);

        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $slug = \Illuminate\Support\Str::slug(str_replace('/', '-', $request->nombre));
            $filename = $slug . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('productos', $filename, 'public');
            $validated['imagen'] = '/storage/' . $path;
        }

        $validated['estado'] = $request->has('estado');

        Producto::create($validated);

        return redirect()->route('productos.index')->with('success', 'Producto creado exitosamente.');
    }

    public function edit(Producto $producto)
    {
        $categorias = \App\Models\Categoria::orderBy('nombre')->get();
        return view('admin.productos.form', compact('producto', 'categorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:50|unique:productos,sku,' . $producto->id,
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'unidad_medida' => 'required|string|max:30',
            'precio_venta' => 'required|numeric|min:0',
            'precio_compra' => 'required|numeric|min:0',
            'stock_actual' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
            'estado' => 'boolean',
            'categoria_id' => 'required|exists:categorias,id',
            'imagen' => 'nullable|image|max:2048',
            'cantidad_por_mayor' => 'nullable|integer|min:1',
            'descuento_por_mayor' => 'nullable|numeric|min:0|max:100'
        ]);

        if ($request->hasFile('imagen')) {
            // Delete old image if needed (not implementing here for simplicity, but good practice)
            $file = $request->file('imagen');
            $slug = \Illuminate\Support\Str::slug(str_replace('/', '-', $request->nombre));
            $filename = $slug . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('productos', $filename, 'public');
            $validated['imagen'] = '/storage/' . $path;
        }

        $validated['estado'] = $request->has('estado');

        $producto->update($validated);

        return redirect()->route('productos.index')->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();
        return redirect()->route('productos.index')->with('success', 'Producto eliminado exitosamente.');
    }
}
