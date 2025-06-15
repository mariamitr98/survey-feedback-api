<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
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

    /**
     * Submit answers to a survey (all or nothing).
     *
     * @var Request: The caller HttpRequest data
     */
    public function submitAnswers(Request $request, string $id) {

        try{
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

            $auth_user_id = auth('api')->user()->id;

            // Foreach catch erros if exist
            $answers =$request->input('answers');

            foreach($answers as $answer) {
                // Get survey question by question_id
                $question = $survey->questions->where('id', $answer['question_id'])->first();

                if(!$question) {
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
                    array_push($errors, 'Question ID ' . $question->id . ' unsupported type.');
                }
            }

            // At least one error exist in array terminate with bad request
            if(!empty($errors)) {
                return response()->json(['errors' => $errors], 400);
            }

            DB::transaction(function () use ($answers, $survey, $auth_user_id) {
                foreach ($answers as $answer) {
                    //find question to be updated
                    $question = $survey->questions->where('id', $answer['question_id'])->first();
            
                    if ($question) { 
                        // create
                        $question->answer->create([
                            'responder_id'  => $auth_user_id,
                            'response_data' => json_encode($answer['value']),
                        ]);
                    }
                }
            });

            return response()->json([], 201);
        }
        catch (\Throwable) {
            return response()->json(['error' => 'Something went wrong during survey submition.'], 500);
        }
    }

}
