<?php

namespace App\Http\Controllers;

use App\Models\Questions;
use App\Models\Tests;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class QuestionsController extends Controller {
    public function list($authId) {
        try {
            $auth = Cache::get($authId);
            return view('certification.questions.list', [
                'questions' => Questions::where('portal', $auth->portal)->get(),
                'auth' => Cache::get($authId)
            ]);
        } catch (Exception $exception) {
            report($exception);
            abort($exception->getCode(), $exception->getMessage());
        }
    }

    public function create($authId) {
        try {
            return view('certification.questions.create', [
                'auth' => Cache::get($authId)
            ]);
        } catch (Exception $exception) {
            report($exception);
            abort($exception->getCode(), $exception->getMessage());
        }
    }

    public function store(Request $request) {

        $data = $request->validate([
            'portal' => ['required', 'integer', 'exists:bitrix_portals,id'],
            'title' => ['required', 'string'],
            'text' => ['required', 'string'],
            'time_min' => ['required', 'string', 'min:0', 'max:59'],
            'time_sec' => ['required', 'string', 'min:0', 'max:59'],
            'tags' => ['nullable', 'array'],
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
            'time' => $data['time_min'] * 60 + $data['time_sec'],
            'title' => $data['title'],
            'text' => $data['text'],
            'tags' => $data['tags'] ?? null,
            'answers' => $answerArr,
        ];

        $newQuestion = Questions::create($dataCreate);

        return response()->json([
            'success' => true,
            'question' => $newQuestion->toArray()
        ]);

    }

    public function show(Questions $question, $authId, Request $request) {
        $auth = Cache::get($authId);
        try {
            if(empty($question)) throw new Exception(__("Выбранный вопрос не существует"));
            if($auth->portal != $question->portal) throw new Exception(__("у Вас нет доступа для редактирования вопроса"));
            return view('certification.questions.show', [
                'auth' => Cache::get($authId),
                'question' => $question,
                'request' => $request->all(),
            ]);
        } catch (Exception $exception) {
            report($exception);
            return response()->view('errorPage', [
                'message' => $exception->getMessage(),
                'auth' => $auth
            ], 500);
        }
    }

    public function update(Request $request, Questions $question, $authId) {
        try {
            $data = $request->validate([
                'portal' => ['required', 'integer', 'exists:bitrix_portals,id'],
                'title' => ['required', 'string'],
                'text' => ['required', 'string'],
                'time_min' => ['required', 'string', 'min:0', 'max:59'],
                'time_sec' => ['required', 'string', 'min:0', 'max:59'],
                'tags' => ['nullable', 'array'],
                'answerText' => ['required', 'array'],
                'correctAnswer' => ['required', 'array'],
            ]);

            $auth = Cache::get($authId);
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
                'time' => $data['time_min'] * 60 + $data['time_sec'],
                'title' => $data['title'],
                'text' => $data['text'],
                'tags' => $data['tags'] ?? null,
                'answers' => $answerArr,
            ];

            $question->update($dataUpdate);

            return redirect()->route('questions.show', [
                'auth_id' => $authId,
                'question' => $question->id
            ])->with([
                'success' => true
            ]);

        } catch (Exception $exception) {
            report($exception);

            return redirect()->route('questions.show', [
                'auth_id' => $authId,
                'question' => $question->id
            ])->with([
                'error' => true
            ]);
        }
    }

    public function destroy(Questions $question, $authId) {

        if(empty($question)) {
            return response()->json([
                'message' => __('Вопрос не существует')
            ], 404);
        }

        $auth = Cache::get($authId);

        if($auth->portal != $question->portal) {
            return response()->json([
                'message' => __('Нет доступа к удалению вопроса')
            ], 403);
        }

        if (self::searchQuestionOnTests($question)) {
            return response()->json([
                'message' => __('Запрещено удаление вопроса который используется в одном или нескольких тестах')
            ], 403);
        }

        $question->delete();

        return response()->json([
            'message' => __('Вопрос успешно удален')
        ]);
    }

    private function searchQuestionOnTests(object $question) {
        return Tests::whereJsonContains('questions', ['id' => $question->id])->exists();
    }
}
