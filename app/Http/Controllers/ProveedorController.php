<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ProveedorController extends Controller
{
    public function index()
    {
        $proveedores = Proveedor::with('user')->paginate(10);
        return view('admin.proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('admin.proveedores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_compania' => 'required|string|max:200',
            'ruc' => 'required|string|max:30|unique:proveedors',
            'telefono' => 'required|string|max:50',
            'email' => 'required|email|max:150|unique:users,email',
            'direccion' => 'required|string',
        ]);

        // Crear el usuario asociado con contraseña aleatoria; el proveedor
        // debe usar "Olvidé mi contraseña" para establecer la suya.
        $user = User::create([
            'name' => $request->nombre_compania,
            'email' => $request->email,
            'password' => Hash::make(Str::random(32)),
        ]);
        $user->assignRole('Proveedor');

        Password::sendResetLink(['email' => $user->email]);

        // Crear el proveedor
        Proveedor::create([
            'user_id' => $user->id,
            'nombre_compania' => $request->nombre_compania,
            'ruc' => $request->ruc,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'direccion' => $request->direccion,
        ]);

        return redirect()->route('proveedores.index')->with('success', 'Proveedor registrado exitosamente.');
    }

    public function show(Proveedor $proveedor)
    {
        return view('admin.proveedores.show', compact('proveedor'));
    }

    public function edit(Proveedor $proveedore)
    {
        $proveedor = $proveedore;
        return view('admin.proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, Proveedor $proveedore)
    {
        $proveedor = $proveedore;
        $request->validate([
            'nombre_compania' => 'required|string|max:200',
            'ruc' => 'required|string|max:30|unique:proveedors,ruc,' . $proveedor->id,
            'telefono' => 'required|string|max:50',
            'direccion' => 'required|string',
        ]);

        $proveedor->update([
            'nombre_compania' => $request->nombre_compania,
            'ruc' => $request->ruc,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
        ]);

        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado.');
    }

    public function destroy(Proveedor $proveedore)
    {
        $proveedore->user()->delete(); // Elimina en cascada
        return redirect()->route('proveedores.index')->with('success', 'Proveedor eliminado.');
    }
}
