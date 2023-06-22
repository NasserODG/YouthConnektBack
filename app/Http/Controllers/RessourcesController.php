<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use App\Models\Ressources;

/**
 * @OA\Info(
 *     title="Documentation de l'API",
 *     version="1.0.0"
 * )
 */


class RessourcesController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     /**
 * @OA\Schema(
 *     schema="RessourcesListResponse",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID de la ressource"
 *     ),
 *     @OA\Property(
 *         property="titre",
 *         type="string",
 *         description="Titre de la ressource"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Description de la ressource"
 *     ),
 *     @OA\Property(
 *         property="type",
 *         type="string",
 *         description="Type de la ressource"
 *     ),
 *     @OA\Property(
 *         property="lien",
 *         type="string",
 *         description="Lien vers la ressource"
 *     )
 * )
 */


     /**
 * @OA\Get(
 *     path="/api/ressources",
 *     tags={"Ressources"},
 *     summary="Liste des ressources",
 *     description="Récupère la liste de toutes les ressources.",
 *     @OA\Response(
 *         response=200,
 *         description="Liste des ressources",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(schema="RessourcesListResponse")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Aucune ressource disponible",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 description="Message d'erreur",
 *                 type="string"
 *             )
 *         )
 *     )
 * )
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


     /**
 * @OA\Schema(
 *     schema="RessourceCreateRequest",
 *     required={"titre", "description", "file"},
 *     @OA\Property(
 *         property="titre",
 *         type="string",
 *         description="Titre de la ressource"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Description de la ressource"
 *     ),
 *     @OA\Property(
 *         property="file",
 *         type="string",
 *         description="Fichier de la ressource"
 *     )
 * )
 */



     /**
     * @OA\Post(
     *     path="/api/ressources",
     *     tags={"Ressources"},
     *     summary="Création d'une ressource",
     *     description="Crée une nouvelle ressource.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(schema="RessourceCreateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ressource créée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 description="Message de confirmation",
     *                 type="string"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Mauvaise requête",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 description="Message d'erreur",
     *                 type="string"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Échec de la validation des données",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="errors",
     *                 description="Liste des erreurs de validation",
     *                 type="array",
     *                 @OA\Items(type="string")
     *             )
     *         )
     *     )
     * )
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

     /**
 * @OA\Schema(
 *     schema="RessourceShowRequest",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID de la ressource"
 *     ),
 *     @OA\Property(
 *         property="titre",
 *         type="string",
 *         description="Titre de la ressource"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Description de la ressource"
 *     ),
 *     @OA\Property(
 *         property="lien",
 *         type="string",
 *         description="Lien vers le fichier de la ressource"
 *     ),
 *     @OA\Property(
 *         property="type",
 *         type="string",
 *         description="Type de la ressource"
 *     )
 * )
 */



     /**
 * @OA\Get(
 *     path="/api/ressources/{id}",
 *     tags={"Ressources"},
 *     summary="Affichage d'une ressource",
 *     description="Affiche les détails d'une ressource spécifiée par son ID.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID de la ressource",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Détails de la ressource",
 *         @OA\JsonContent(schema="RessourceShowRequest")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Ressource non trouvée",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 description="Message d'erreur",
 *                 type="string"
 *             )
 *         )
 *     )
 * )
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


     /**
 * @OA\Schema(
 *     schema="RessourceUpdateRequest",
 *     @OA\Property(
 *         property="titre",
 *         type="string",
 *         description="Nouveau titre de la ressource"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Nouvelle description de la ressource"
 *     ),
 *     @OA\Property(
 *         property="file",
 *         type="string",
 *         description="Nouveau fichier de la ressource"
 *     )
 * )
 */


     /**
 * @OA\Put(
 *     path="/api/ressources/{id}",
 *     tags={"Ressources"},
 *     summary="Mise à jour d'une ressource",
 *     description="Met à jour les informations d'une ressource spécifiée par son ID.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID de la ressource",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="Données de la ressource à mettre à jour",
 *         @OA\JsonContent(schema="RessourceUpdateRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Ressource mise à jour avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 description="Message de succès",
 *                 type="string"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Ressource non trouvée",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 description="Message d'erreur",
 *                 type="string"
 *             )
 *         )
 *     )
 * )
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
        }
        $ressource->save();

        return response()->json($ressource, 200);
    }




    /**
     * Remove the specified resource from storage.
     */

     


     /**
     * @OA\Delete(
     *     path="/api/ressources/{id}",
     *     tags={"Ressources"},
     *     summary="Suppression d'une ressource",
     *     description="Supprime une ressource spécifiée par son ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la ressource",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ressource supprimée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 description="Message de confirmation",
     *                 type="string"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ressource non trouvée",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 description="Message d'erreur",
     *                 type="string"
     *             )
     *         )
     *     )
     * )
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
