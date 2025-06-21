<?php

namespace App\Http\Controllers;

use App\Models\Schedule; // Assicurati che questo 'use' sia presente
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; // Per risposte JSON standard
use Illuminate\Support\Facades\Auth; // Per accedere all'utente autenticato

class ScheduleController extends Controller
{
    public function __construct()
    {
        // Questo middleware assicura che solo gli utenti autenticati con Sanctum
        // possano accedere a qualsiasi metodo di questo controller.
        // NON rimuoverlo se vuoi proteggere le tue API.
        $this->middleware('auth:sanctum');
    }

    /**
     * Recupera tutti i programmi (schedule) dell'utente autenticato.
     * Questa rotta risponde a: GET /api/schedules
     */
    public function index(): JsonResponse
    {
        // La policy 'viewAny' è implicita qui perché recuperiamo solo
        // i programmi dell'utente autenticato.
        $schedules = Auth::user()->schedules()->get(); // Usa ->get() per eseguire la query

        return response()->json($schedules);
    }

    /**
     * Salva un nuovo programma (schedule).
     * Questa rotta risponde a: POST /api/schedules
     */
    public function store(Request $request): JsonResponse
    {
        // Autorizza l'azione 'create' usando la SchedulePolicy.
        // Questo verificherà se l'utente autenticato ha il permesso di creare un programma.
        $this->authorize('create', Schedule::class);

        // Convalida i dati in ingresso dalla richiesta.
        $validatedData = $request->validate([
            'day' => 'required|string|max:255', // Es: "Lunedì"
            'hour' => 'required|string|max:255', // Es: "09:00"
            'materia' => 'required|string|max:255',
            'docente' => 'nullable|string|max:255', // Campo opzionale
            'aula' => 'nullable|string|max:255', // Campo opzionale
        ]);

        // Crea il programma associandolo direttamente all'utente autenticato.
        $schedule = Auth::user()->schedules()->create($validatedData);

        // Restituisce il programma appena creato con uno stato HTTP 201 (Created).
        return response()->json($schedule, 201);
    }

    /**
     * Aggiorna un programma (schedule) esistente.
     * Questa rotta risponde a: PUT/PATCH /api/schedules/{schedule}
     */
    public function update(Request $request, Schedule $schedule): JsonResponse
    {
        // Autorizza l'azione 'update' usando la SchedulePolicy.
        // Questo è FONDAMENTALE per la sicurezza: assicura che l'utente autenticato
        // sia il PROPRIETARIO del programma che sta cercando di modificare.
        $this->authorize('update', $schedule);

        // Convalida i dati in ingresso. 'sometimes' significa che il campo
        // è richiesto solo se presente nella richiesta di aggiornamento.
        $validatedData = $request->validate([
            'day' => 'sometimes|required|string|max:255',
            'hour' => 'sometimes|required|string|max:255',
            'materia' => 'sometimes|required|string|max:255',
            'docente' => 'nullable|string|max:255',
            'aula' => 'nullable|string|max:255',
        ]);

        // Aggiorna il programma con i dati convalidati.
        $schedule->update($validatedData);

        // Restituisce il programma aggiornato.
        return response()->json($schedule);
    }

    /**
     * Elimina un programma (schedule) esistente.
     * Questa rotta risponde a: DELETE /api/schedules/{schedule}
     */
    public function destroy(Schedule $schedule): JsonResponse
    {
        // Autorizza l'azione 'delete' usando la SchedulePolicy.
        // FONDAMENTALE per la sicurezza: assicura che l'utente sia il PROPRIETARIO.
        $this->authorize('delete', $schedule);

        // Elimina il programma dal database.
        $schedule->delete();

        // Restituisce una risposta HTTP 204 (No Content) per indicare il successo
        // senza restituire alcun dato.
        return response()->json(null, 204);
    }
}