<?php

namespace App\Http\Controllers;

use App\Models\Questions;
use App\Models\Tests;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TestsController extends Controller {

    public function list($memberId) {
        try {
            $auth = Cache::get($memberId);
            return view('certification.tests.list', [
                'tests' => Tests::where('portal', $auth->portal)->paginate(10),
                'auth' => Cache::get($memberId)
            ]);
        } catch (Exception $exception) {
            report($exception);
            abort($exception->getCode(), $exception->getMessage());
        }
    }

    public function create($memberId) {
        try {
            $auth = Cache::get($memberId);
            return view('certification.tests.create', [
                'auth' => $auth,
                'questions' => Questions::where('portal', $auth->portal)->get()->toArray(),
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
            'maximum_time_min' => ['required', 'integer', 'min:0', 'max:59'],
            'maximum_time_sec' => ['required', 'integer', 'min:0', 'max:59'],
            'test_maximum_score' => ['required', 'integer', 'min:1'],
            'test_passing_score' => ['required', 'integer', 'min:1'],
            'descriptions' => ['nullable', 'string'],
            'question_maximum_time_min' => ['nullable', 'array'],
            'question_maximum_time_sec' => ['nullable', 'array'],
            'question_score' => ['required', 'array'],
            'question' => ['required', 'array'],
            'skipping' => ['nullable', 'string'],
            'ranging' => ['nullable', 'string'],
        ]);

        if($data['test_maximum_score'] < $data['test_passing_score']) {
            return response()->json([
                'message' => __('Максимальный балл за тест, не может быть меньше чем проходной балл')
            ],400);
        }

        if(count($data['question_score']) != count($data['question'])) {
            return response()->json([
                'message' => __('Запрещено публиковать вопросы без оценки и\или указывать оценку без указания вопроса.')
            ],400);
        }

        if(array_sum($data['question_score']) != $data['test_maximum_score']) {
            return response()->json([
                'message' => __('Сумма баллов по каждому вопросу не может быть меньше максимального балла за тест.')
            ],400);
        }

        $countRepeatQuestions = array_filter(array_count_values($data['question']), function ($element) {
            return $element > 1;
        });

        if(!empty($countRepeatQuestions)) {
            return response()->json([
                'message' => __('В тесте запрещено указывать вопрос более одного раза.')
            ],400);
        }

        $testTimeSec = $data['maximum_time_min'] * 60 + $data['maximum_time_sec'];

        $totalQuestions['min'] = array_sum($data['question_maximum_time_min']);
        $totalQuestions['sec'] = array_sum($data['question_maximum_time_sec']);
        $totalQuestionsTime = $totalQuestions['min'] * 60 + $totalQuestions['sec'];

        if($testTimeSec != $totalQuestionsTime) {
            return response()->json([
                'message' => __('Время выполнения теста не может отличаться от общего времени ответа по каждому вопросу.')
            ],400);
        }

        foreach ($data['question'] as $key => $question) {
            $questions[] = [
                'id' => (int) $question,
                'score' => (int) $data['question_score'][$key],
                'time' => $data['question_maximum_time_min'][$key] * 60 + $data['question_maximum_time_sec'][$key]
            ];
        }

        $dataCreate = [
            'portal' => $data['portal'],
            'title' => $data['title'],
            'description' => $data['descriptions'],
            'maximum_time' => $testTimeSec,
            'maximum_score' => $data['test_maximum_score'],
            'passing_score' => $data['test_passing_score'],
            'skipping' => isset($data['skipping']) && $data['skipping'] == 'on' ? 1 : 0,
            'ranging' => isset($data['ranging']) && $data['ranging'] == 'on' ? 1 : 0,
            'questions' => $questions ?? [],
        ];

        Tests::create($dataCreate);

        return response()->json([
            'success' => true
        ]);
    }

    public function show(Tests $tests) {
        return $tests;
    }

    public function update(Request $request, Tests $tests) {
        $data = $request->validate([
            'title' => ['required'],
            'descriptions' => ['required'],
            'maximum_score' => ['required', 'integer'],
            'passing_score' => ['required', 'integer'],
            'unanswered' => ['boolean'],
            'skipping' => ['boolean'],
            'ranging' => ['boolean'],
            'questions' => ['required'],
            'maximum_time' => ['required', 'integer'],
        ]);

        $tests->update($data);

        return $tests;
    }

    public function destroy(Tests $tests) {
        $tests->delete();

        return response()->json();
    }
}
