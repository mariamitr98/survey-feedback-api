<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Survey;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SurveyController extends Controller
{
    /**
     * Return all active surveys from database.
     */
    public function getActiveSurveys()
    {
        // Retrieve item from cache and if requested item doesn't exist retrieve 
        // from database and store it to cache
        $activeSurveys = Cache::remember('active_surveys', 300, function () {
            return Survey::where('status', 'active')->get();
        });

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

    /**
     * Submit answers to a survey (all or nothing).
     *
     * @var Request: The caller HttpRequest data
     */
    public function submitAnswers(Request $request, string $id)
    {
        //Log submissions
        $log_data = [
            "responder_ip" => $request->ip(),
            "responder_id" => auth('api')->user()->id,
            "submited_data" => $request->answers,
        ];

        $file_path = 'submissions-log/survey_'.$id.'_'.date("Y_m_d_H_i_s").'.json';

        Storage::disk('local')->put($file_path, json_encode($log_data));

        try {
            $validator = Validator::make($request->all(), [
                'answers' => 'required|array',
                'answers.*.question_id' => 'required|distinct',
                'answers.*.value' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $survey = Survey::with('questions')->where('status', 'active')->find($id);

            // Survey not exist - no found in database
            if (!$survey) {
                return response()->json(['error' => 'Survey not found.'], 404);
            }

            $errors = [];
            $answers = $request->input('answers');

            // Foreach catch erros if exist
            foreach ($answers as $answer) {
                // Get survey question by question_id
                $question = $survey->questions->where('id', $answer['question_id'])->first();

                if (!$question) {
                    array_push($errors, 'Question with ID ' . $answer['question_id'] . ' not found in survey with ID ' . $id);
                    break;
                }

                // Switch validation base on type
                $valid = match ($question->type) {
                    'text' => is_string($answer['value']),
                    'scale' => is_int($answer['value']),
                    'multiple_choice' => is_array($answer['value']),
                    default => false,
                };

                if (!$valid) {
                    array_push($errors, 'Answer for Question ID ' . $question->id . ' unsupported type. Corrected type is : ' . $question->type);
                }
            }

            // At least one error exist in array terminate with bad request
            if (!empty($errors)) {
                return response()->json(['errors' => $errors], 400);
            }

            $responder_id = auth('api')->user()->id;

            // Create db transaction 
            DB::transaction(function () use ($answers, $survey, $responder_id) {
                foreach ($answers as $answer) {
                    //find question to be updated
                    $question = $survey->questions->where('id', $answer['question_id'])->first();

                    if ($question) {
                        // Store the answer into the database
                        $question->answer()->create([
                            'responder_id'  => $responder_id,
                            'response_data' => json_encode($answer['value']),
                        ]);
                    }
                }
            });

            return response()->json(["Created"], 201);
        } catch (\Throwable $e) {
            Log::error($e); 
            return response()->json(['error' => 'Something went wrong during survey submition.'], 500);
        }
    }
}
