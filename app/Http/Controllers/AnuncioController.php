<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anuncio;
use App\Models\User;
use App\Notifications\AnuncioCreado;
use App\Notifications\AnuncioAprobado;
use App\Notifications\AnuncioDenegado;

class AnuncioController extends Controller
{
    public function index()
    {
        $anuncios = Anuncio::orderByDesc('created_at')->paginate(15);
        return view('admin.anuncios.index', compact('anuncios'));
    }

    public function create()
    {
        return view('admin.anuncios.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:150',
            'contenido' => 'nullable|string',
            'imagen' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('anuncios', 'public');
            $validated['imagen'] = $path;
        }

        $validated['supervisor_id'] = auth()->id();
        $validated['estado'] = 'pendiente';

        $anuncio = Anuncio::create($validated);

        // Notificar a SuperAdmin o Administrador
        $admins = User::role(['Administrador', 'SuperAdmin'])->get();
        foreach ($admins as $admin) {
            $admin->notify(new AnuncioCreado($anuncio));
        }

        return redirect()->route('anuncios.index')->with('success', 'Anuncio creado y pendiente de aprobación.');
    }

    public function edit(Anuncio $anuncio)
    {
        return view('admin.anuncios.form', compact('anuncio'));
    }

    public function update(Request $request, Anuncio $anuncio)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:150',
            'contenido' => 'nullable|string',
            'imagen' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('anuncios', 'public');
            $validated['imagen'] = $path;
        }

        // Si se edita, vuelve a estado pendiente
        $validated['estado'] = 'pendiente';

        $anuncio->update($validated);

        // Notificar a SuperAdmin o Administrador
        $admins = User::role(['Administrador', 'SuperAdmin'])->get();
        foreach ($admins as $admin) {
            $admin->notify(new AnuncioCreado($anuncio));
        }

        return redirect()->route('anuncios.index')->with('success', 'Anuncio actualizado y pendiente de aprobación.');
    }

    public function destroy(Anuncio $anuncio)
    {
        $anuncio->delete();
        return redirect()->route('anuncios.index')->with('success', 'Anuncio eliminado exitosamente.');
    }

    public function aprobar(Anuncio $anuncio)
    {
        $anuncio->update(['estado' => 'aprobado']);
        
        $supervisor = User::find($anuncio->supervisor_id);
        if ($supervisor) {
            $supervisor->notify(new AnuncioAprobado($anuncio));
        }

        return redirect()->route('anuncios.index')->with('success', 'Anuncio aprobado y publicado.');
    }

    public function denegar(Anuncio $anuncio)
    {
        $anuncio->update(['estado' => 'rechazado']);
        
        $supervisor = User::find($anuncio->supervisor_id);
        if ($supervisor) {
            $supervisor->notify(new AnuncioDenegado($anuncio));
        }

        return redirect()->route('anuncios.index')->with('success', 'Anuncio denegado.');
    }
}
