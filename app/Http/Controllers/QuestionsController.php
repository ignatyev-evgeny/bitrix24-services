<?php

namespace App\Http\Controllers;

use App\Models\Questions;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class QuestionsController extends Controller {
    public function list($memberId) {
        try {
            $auth = Cache::get($memberId);
            return view('certification.questions.list', [
                'questions' => Questions::where('portal', $auth->portal)->paginate(10),
                'auth' => Cache::get($memberId)
            ]);
        } catch (Exception $exception) {
            report($exception);
            abort($exception->getCode(), $exception->getMessage());
        }
    }

    public function create($memberId) {
        try {
            return view('certification.questions.create', [
                'auth' => Cache::get($memberId)
            ]);
        } catch (Exception $exception) {
            report($exception);
            abort($exception->getCode(), $exception->getMessage());
        }
    }

    public function store(Request $request, $memberId) {
        try {

            $data = $request->validate([
                'portal' => ['required', 'integer', 'exists:bitrix_portals,id'],
                'title' => ['required', 'string'],
                'text' => ['required', 'string'],
                'serializedTags' => ['nullable', 'string'],
                'answerText' => ['required', 'array'],
                'correctAnswer' => ['required', 'array'],
            ]);

            $answerArr = [];
            foreach ($data['answerText'] as $key => $answer) {
                $answerArr[$key] = [
                    'title' => $answer,
                    'correct' => isset($data['correctAnswer'][$key]),
                ];
            }

            $dataCreate = [
                'portal' => $data['portal'],
                'title' => $data['title'],
                'text' => $data['text'],
                'tags' => !empty($data['serializedTags']) ? explode(',', $data['serializedTags']) : null,
                'answers' => $answerArr,
            ];

            Questions::create($dataCreate);

            return redirect()->route('questions.list', [
                'member_id' => $memberId
            ]);

        } catch (Exception $exception) {
            report($exception);
            abort($exception->getCode(), $exception->getMessage());
        }
    }

    public function show(Questions $question, $memberId) {
        $auth = Cache::get($memberId);
        try {
            if(empty($question)) throw new Exception(__("Выбранный вопрос не существует"));
            if($auth->portal != $question->portal) throw new Exception(__("у Вас нет доступа для редактирования вопроса"));
            return view('certification.questions.show', [
                'auth' => Cache::get($memberId),
                'question' => $question
            ]);
        } catch (Exception $exception) {
            report($exception);
            return response()->view('errorPage', [
                'message' => $exception->getMessage(),
                'auth' => $auth
            ], 500);
        }
    }

    public function update(Request $request, Questions $question, $memberId) {
        try {
            $data = $request->validate([
                'portal' => ['required', 'integer', 'exists:bitrix_portals,id'],
                'title' => ['required', 'string'],
                'text' => ['required', 'string'],
                'serializedTags' => ['nullable', 'string'],
                'answerText' => ['required', 'array'],
                'correctAnswer' => ['required', 'array'],
            ]);

            $auth = Cache::get($memberId);
            if(empty($question)) throw new Exception(__("Выбранный вопрос не существует"));
            if($auth->portal != $question->portal) throw new Exception(__("у Вас нет доступа для редактирования вопроса"));

            $answerArr = [];
            foreach ($data['answerText'] as $key => $answer) {
                $answerArr[$key] = [
                    'title' => $answer,
                    'correct' => isset($data['correctAnswer'][$key]),
                ];
            }

            $dataUpdate = [
                'portal' => $data['portal'],
                'title' => $data['title'],
                'text' => $data['text'],
                'tags' => !empty($data['serializedTags']) ? explode(',', $data['serializedTags']) : null,
                'answers' => $answerArr,
            ];

            $question->update($dataUpdate);

            return redirect()->route('questions.show', [
                'member_id' => $memberId,
                'question' => $question->id
            ])->with([
                'success' => true
            ]);

        } catch (Exception $exception) {
            report($exception);

            return redirect()->route('questions.show', [
                'member_id' => $memberId,
                'question' => $question->id
            ])->with([
                'error' => true
            ]);
        }
    }

    public function destroy(Questions $questions) {
        $questions->delete();
        return response()->json();
    }
}
