<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use App\Models\Ressources;


class RessourcesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ressources = Ressources::all();
        if ($ressources->isEmpty()) {
            return response()->json(['message' => 'Aucune ressource disponible'], 200);
        }
        return response()->json($ressources);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'titre' => 'required|string|max:255',
        'description' => 'required|string|max:255',
        'file' => 'mimes:pdf,doc,docx,mp4,avi,3gp|max:10240',
    ]);

    if ($validator->fails()) {
        return response(['errors' => $validator->errors()->all()], 422);
    }

    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $path = $file->storeAs('public/ressources', $fileName);

        if (in_array($extension, ['pdf', 'doc', 'docx', 'PDF', 'DOC', 'DOCX'])) {
            $type = 'document';
        } elseif (in_array($extension, ['mp4', 'avi', '3gp', 'MP4', 'AVI', '3GP'])) {
            $type = 'vidéo';
        } else {
            $type = 'inconnu';
        }

        $ressource = new Ressources;
        $ressource->titre = $request->input('titre');
        $ressource->description = $request->input('description');
        $ressource->lien = $path;
        $ressource->type = $type;
        $ressource->save();


        return response(['message' => 'La ressource a été enregistrée avec succès'], 200);
    }

    return response(['message' => 'Aucun fichier n\'a été trouvé'], 400);
}

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $ressource = Ressources::find($id);
        if (!$ressource) {
            return response()->json(['message' => 'Ressource non trouvée'], 404);
        }
        return response()->json($ressource);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        $ressource = Ressources::find($id);

        if (!$ressource) {
            return response()->json(['message' => 'Ressource non trouvée'], 404);
        }

        $ressource->titre = $request->input('titre');
        $ressource->description = $request->input('description');

        if ($request->hasFile('file')) {
            // Supprimer l'ancien fichier
            Storage::delete($ressource->lien);

            // Enregistrer le nouveau fichier
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $filePath = $file->storeAs('public/ressources', $fileName);
            $ressource->lien = $filePath;

            // if (in_array($extension, ['pdf', 'doc', 'docx', 'PDF', 'DOC', 'DOCX'])) {
            //     $type = 'document';
            // } elseif (in_array($extension, ['mp4', 'avi', '3gp', 'MP4', 'AVI', '3GP'])) {
            //     $type = 'vidéo';
            // } else {
            //     $type = 'inconnu';
            // }
        }

        //$ressource->type = $type;
        $ressource->save();

        return response()->json($ressource, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $ressource = Ressources::find($id);
        if (!$ressource) {
            return response()->json(['message' => 'Ressource non trouvée'], 404);
        }

        // Supprimer le fichier associé
        Storage::delete($ressource->lien);
        Storage::delete('public/ressources/' . $ressource->titre);

        $ressource->delete();

        return response()->json(['message' => 'Ressource supprimée avec succès'], 200);
    
    }
}
