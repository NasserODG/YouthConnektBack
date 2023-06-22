<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


/**
 * @OA\Tag(
 *     name="Authentification",
 *     description="Endpoints pour l'Authentification des utilisateurs"
 * )
 */

class AuthController extends Controller
{


    /**
 * @OA\Post(
 *     path="/api/login",
 *     tags={"Auth"},
 *     summary="Connexion de l'utilisateur",
 *     description="Permet à l'utilisateur de se connecter en fournissant son email et mot de passe.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="email",
 *                     description="Adresse email de l'utilisateur",
 *                     type="string",
 *                 ),
 *                 @OA\Property(
 *                     property="mdp",
 *                     description="Mot de passe de l'utilisateur",
 *                     type="string",
 *                 ),
 *                 example={
 *                     "email": "john@example.com",
 *                     "mdp": "secret"
 *                 }
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Connexion réussie",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="token",
 *                 description="Token d'accès personnel",
 *                 type="string"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Paramètres invalides ou mot de passe incorrect ou utilisateur inexistant",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 description="Message d'erreur",
 *                 type="string"
 *               )
 *          )
 *      ),  
 * )
 */


    public function login (Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'mdp' => 'required|string|min:6',
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->mdp, $user->mdp)) {
                $token = $user->createToken('Laravel Personal Access Token')->accessToken;
                $response = ['token' => $token];
                return response($response, 200);
            } else {
                $response = ["message" => "Password mismatch"];
                return response($response, 422);
            }
        } else {
            $response = ["message" =>'User does not exist'];
            return response($response, 422);
        }
    }


    /**
 * @OA\Post(
 *     path="/api/register",
 *     tags={"Auth"},
 *     summary="Inscription d'un nouvel utilisateur",
 *     description="Permet à un nouvel utilisateur de s'inscrire en fournissant ses informations.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="nom",
 *                     description="Nom de l'utilisateur",
 *                     type="string",
 *                 ),
 *                 @OA\Property(
 *                     property="email",
 *                     description="Adresse email de l'utilisateur",
 *                     type="string",
 *                 ),
 *                 @OA\Property(
 *                     property="mdp",
 *                     description="Mot de passe de l'utilisateur",
 *                     type="string",
 *                 ),
 *                 example={
 *                     "nom": "John Doe",
 *                     "email": "john@example.com",
 *                     "mdp": "secret"
 *                 }
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Inscription réussie",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="token",
 *                 description="Token d'accès personnel",
 *                 type="string"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Paramètres invalides",
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
    

    public function register (Request $request) {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'mdp' => 'required|string|min:6',
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        $request['mdp']=Hash::make($request['mdp']);
        $request['remember_token'] = Str::random(10);
        $user = User::create($request->toArray());
        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        $response = ['token' => $token];
        return response($response, 200);
    }



    /**
 * @OA\Post(
 *     path="/api/logout",
 *     tags={"Auth"},
 *     summary="Déconnexion de l'utilisateur",
 *     description="Permet à un utilisateur connecté de se déconnecter.",
 *     security={{"Bearer": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Déconnexion réussie",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 description="Message de confirmation",
 *                 type="string"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non autorisé",
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


    public function logout (Request $request) {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }

    // Autres méthodes d'authentification
}

