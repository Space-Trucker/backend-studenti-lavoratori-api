<?php

namespace App\Http\Controllers;

use App\Models\ImportantDate; // Assicurati che questo 'use' sia presente
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; // Per risposte JSON standard
use Illuminate\Support\Facades\Auth; // Per accedere all'utente autenticato

class ImportantDateController extends Controller
{
    public function __construct()
    {
        // Questo middleware assicura che solo gli utenti autenticati con Sanctum
        // possano accedere a qualsiasi metodo di questo controller.
        $this->middleware('auth:sanctum');
    }

    /**
     * Recupera tutte le date importanti dell'utente autenticato.
     * Questa rotta risponde a: GET /api/important-dates
     */
    public function index(): JsonResponse
    {
        // La policy 'viewAny' è implicita qui perché recuperiamo solo
        // le date importanti dell'utente autenticato.
        $importantDates = Auth::user()->importantDates()->get(); // Usa ->get() per eseguire la query

        return response()->json($importantDates);
    }

    /**
     * Salva una nuova data importante.
     * Questa rotta risponde a: POST /api/important-dates
     */
    public function store(Request $request): JsonResponse
    {
        // Autorizza l'azione 'create' usando la ImportantDatePolicy.
        $this->authorize('create', ImportantDate::class);

        // Convalida i dati in ingresso dalla richiesta.
        $validatedData = $request->validate([
            'type' => 'required|string|max:255', // Es: "Esame", "Scadenza Progetto"
            'date' => 'required|date', // Assicura che il formato sia una data valida
            'description' => 'nullable|string', // Campo opzionale
        ]);

        // Crea la data importante associandola direttamente all'utente autenticato.
        $importantDate = Auth::user()->importantDates()->create($validatedData);

        // Restituisce la data importante appena creata con uno stato HTTP 201 (Created).
        return response()->json($importantDate, 201);
    }

    /**
     * Aggiorna una data importante esistente.
     * Questa rotta risponde a: PUT/PATCH /api/important-dates/{importantDate}
     */
    public function update(Request $request, ImportantDate $importantDate): JsonResponse
    {
        // Autorizza l'azione 'update' usando la ImportantDatePolicy.
        // FONDAMENTALE per la sicurezza: assicura che l'utente sia il PROPRIETARIO.
        $this->authorize('update', $importantDate);

        // Convalida i dati in ingresso. 'sometimes' significa che il campo
        // è richiesto solo se presente nella richiesta di aggiornamento.
        $validatedData = $request->validate([
            'type' => 'sometimes|required|string|max:255',
            'date' => 'sometimes|required|date',
            'description' => 'nullable|string',
        ]);

        // Aggiorna la data importante con i dati convalidati.
        $importantDate->update($validatedData);

        // Restituisce la data importante aggiornata.
        return response()->json($importantDate);
    }

    /**
     * Elimina una data importante esistente.
     * Questa rotta risponde a: DELETE /api/important-dates/{importantDate}
     */
    public function destroy(ImportantDate $importantDate): JsonResponse
    {
        // Autorizza l'azione 'delete' usando la ImportantDatePolicy.
        // FONDAMENTALE per la sicurezza: assicura che l'utente sia il PROPRIETARIO.
        $this->authorize('delete', $importantDate);

        // Elimina la data importante dal database.
        $importantDate->delete();

        // Restituisce una risposta HTTP 204 (No Content).
        return response()->json(null, 204);
    }
}
