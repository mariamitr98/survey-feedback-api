<?php

namespace App\Http\Controllers;

use App\Models\Survey;

class SurveyController extends Controller
{
    /**
     * Return all active surveys from database.
     */
    public function getActiveSurveys()
    {
        $activeSurveys = Survey::where('status', 'active')->get();
        return response()->json(compact('activeSurveys'), 200);
    }

    /**
     * Return details with questions for a specific survey.
     */
    public function getSurveyById(string $id)
    {
        $survey = Survey::with('questions')->where('id', $id)->first();
        return response()->json(compact('survey'), 200);
    }
}
