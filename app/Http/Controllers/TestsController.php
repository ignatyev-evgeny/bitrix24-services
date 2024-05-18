<?php

namespace App\Http\Controllers;

use App\Models\Questions;
use App\Models\Tests;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TestsController extends Controller {

    public function list($authId) {
        try {
            $auth = Cache::get($authId);
            return view('certification.tests.list', [
                'tests' => Tests::where('portal', $auth->portal)->get(),
                'auth' => Cache::get($authId)
            ]);
        } catch (Exception $exception) {
            report($exception);
            abort($exception->getCode(), $exception->getMessage());
        }
    }

    public function create($authId) {
        try {
            $auth = Cache::get($authId);
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
            'maximum_time_min' => ['required', 'string', 'min:0', 'max:59'],
            'maximum_time_sec' => ['required', 'string', 'min:0', 'max:59'],
            'test_maximum_score' => ['required', 'integer', 'min:1'],
            'test_passing_score' => ['required', 'integer', 'min:1'],
            'descriptions' => ['nullable', 'string'],
            'question_maximum_time_min' => ['nullable', 'array'],
            'question_maximum_time_sec' => ['nullable', 'array'],
            'question_score' => ['required', 'array'],
            'question' => ['required', 'array'],
            'skipping' => ['nullable', 'string'],
            'ranging' => ['nullable', 'string'],
            'attempts' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $questionsTotalScore = 0;
        foreach ($data['question'] as $key => $question) {

            if ($question == 'null') {
                return response()->json([
                    'message' => __('Выбор вопроса является обязательным')
                ],400);
            }

            $questions[] = [
                'id' => (int) $question,
                'score' => (int) $data['question_score'][$key],
                'time' => (isset($data['question_maximum_time_min'][$key]) ? (int) $data['question_maximum_time_min'][$key] : 0) * 60 + (isset($data['question_maximum_time_sec'][$key]) ? (int) $data['question_maximum_time_sec'][$key] : 0)
            ];
            $questionsTotalScore += (int) $data['question_score'][$key];
        }

        if($questionsTotalScore < $data['test_passing_score']) {
            return response()->json([
                'message' => __('Максимальный балл за тест, не может быть меньше чем проходной балл')
            ],400);
        }

        if(count($data['question_score']) != count($data['question'])) {
            return response()->json([
                'message' => __('Запрещено публиковать вопросы без оценки и\или указывать оценку без указания вопроса.')
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

        $testTimeSec = (int) $data['maximum_time_min'] * 60 + (int) $data['maximum_time_sec'];
        $totalQuestions['min'] = !empty($data['question_maximum_time_min']) ? array_sum($data['question_maximum_time_min']) : 0;
        $totalQuestions['sec'] = !empty($data['question_maximum_time_sec']) ? array_sum($data['question_maximum_time_sec']) : 0;
        $totalQuestionsTime = $totalQuestions['min'] * 60 + $totalQuestions['sec'];

        if($totalQuestionsTime > $testTimeSec) {
            return response()->json([
                'message' => __('Время выполнения теста не может быть меньше от общего времени ответа по каждому вопросу.')
            ],400);
        }

        $dataCreate = [
            'portal' => $data['portal'],
            'title' => $data['title'],
            'description' => $data['descriptions'],
            'maximum_time' => $testTimeSec,
            'maximum_score' => $questionsTotalScore,
            'passing_score' => $data['test_passing_score'],
            'skipping' => isset($data['skipping']) && $data['skipping'] == 'on' ? 1 : 0,
            'ranging' => isset($data['ranging']) && $data['ranging'] == 'on' ? 1 : 0,
            'questions' => $questions ?? [],
            'attempts' => $data['attempts'],
        ];


        Tests::create($dataCreate);

        return response()->json([
            'success' => true
        ]);
    }

    public function show(Tests $test, $authId) {
        try {
            $auth = Cache::get($authId);
            if($test->portal != $auth->portal) return view('errorAccess');
            return view('certification.tests.show', [
                'auth' => $auth,
                'test' => $test,
                'questions' => Questions::where('portal', $auth->portal)->get()->toArray(),
            ]);
        } catch (Exception $exception) {
            report($exception);
            abort($exception->getCode(), $exception->getMessage());
        }
    }

    public function update(Request $request, Tests $test) {

        $data = $request->validate([
            'portal' => ['required', 'integer', 'exists:bitrix_portals,id'],
            'title' => ['required', 'string'],
            'maximum_time_min' => ['required', 'string', 'min:0', 'max:59'],
            'maximum_time_sec' => ['required', 'string', 'min:0', 'max:59'],
            'test_passing_score' => ['required', 'integer', 'min:1'],
            'descriptions' => ['nullable', 'string'],
            'question_maximum_time_min' => ['nullable', 'array'],
            'question_maximum_time_sec' => ['nullable', 'array'],
            'question_score' => ['required', 'array'],
            'question' => ['required', 'array'],
            'skipping' => ['nullable', 'string'],
            'ranging' => ['nullable', 'string'],
            'attempts' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $questionsTotalScore = 0;
        foreach ($data['question'] as $key => $question) {

            if ($question == 'null') {
                return response()->json([
                    'message' => __('Выбор вопроса является обязательным')
                ],400);
            }

            $questions[] = [
                'id' => (int) $question,
                'score' => (int) $data['question_score'][$key],
                'time' => (isset($data['question_maximum_time_min'][$key]) ? (int) $data['question_maximum_time_min'][$key] : 0) * 60 + (isset($data['question_maximum_time_sec'][$key]) ? (int) $data['question_maximum_time_sec'][$key] : 0)
            ];
            $questionsTotalScore += (int) $data['question_score'][$key];
        }

        if($questionsTotalScore < $data['test_passing_score']) {
            return response()->json([
                'message' => __('Максимальный балл за тест, не может быть меньше чем проходной балл')
            ],400);
        }

        if(count($data['question_score']) != count($data['question'])) {
            return response()->json([
                'message' => __('Запрещено публиковать вопросы без оценки и\или указывать оценку без указания вопроса.')
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

        $testTimeSec = (int) $data['maximum_time_min'] * 60 + (int) $data['maximum_time_sec'];
        $totalQuestions['min'] = !empty($data['question_maximum_time_min']) ? array_sum($data['question_maximum_time_min']) : 0;
        $totalQuestions['sec'] = !empty($data['question_maximum_time_sec']) ? array_sum($data['question_maximum_time_sec']) : 0;
        $totalQuestionsTime = $totalQuestions['min'] * 60 + $totalQuestions['sec'];

        if($totalQuestionsTime > $testTimeSec) {
            return response()->json([
                'message' => __('Время выполнения теста не может быть меньше от общего времени ответа по каждому вопросу.')
            ],400);
        }

        $dataUpdate = [
            'portal' => (int) $data['portal'],
            'title' => $data['title'],
            'description' => $data['descriptions'],
            'maximum_time' => $testTimeSec,
            'maximum_score' => $questionsTotalScore,
            'passing_score' => (int) $data['test_passing_score'],
            'skipping' => isset($data['skipping']) && $data['skipping'] == 'on' ? 1 : 0,
            'ranging' => isset($data['ranging']) && $data['ranging'] == 'on' ? 1 : 0,
            'questions' => $questions ?? [],
            'attempts' => $data['attempts'],
        ];

        $test->update($dataUpdate);

        return response()->json([
            'success' => true
        ]);
    }

    public function destroy(Tests $test) {
        $test->delete();
        return redirect()->back();
    }
}
