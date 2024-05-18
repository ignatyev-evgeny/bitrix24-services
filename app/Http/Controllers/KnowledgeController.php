<?php

namespace App\Http\Controllers;

use App\Models\Knowledge;
use App\Models\Questions;
use App\Models\Tests;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class KnowledgeController extends Controller {

    public function list($authId) {
        try {
            $auth = Cache::get($authId);
            return view('certification.knowledge.list', [
                'knowledge' => Knowledge::where('portal', $auth->portal)->paginate(10),
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
            return view('certification.knowledge.create', [
                'auth' => $auth,
                'questions' => Questions::where('portal', $auth->portal)->get(),
                'tests' => Tests::where('portal', $auth->portal)->get()
            ]);
        } catch (Exception $exception) {
            report($exception);
            abort($exception->getCode(), $exception->getMessage());
        }
    }
    public function store(Request $request) {

        $data = $request->validate([
            'portal' => ['required', 'integer', 'exists:bitrix_portals,id'],
            'title' => ['required'],
            'description' => ['nullable'],
            'tags' => ['nullable', 'array'],
            'questions' => ['nullable', 'array'],
            'tests' => ['nullable', 'array'],
        ]);

        try {
            DB::beginTransaction();
            Knowledge::create($data);
            DB::commit();

            return response()->json([
                'success' => true
            ]);

        } catch (Exception $exception) {
            DB::rollBack();
            report($exception);
            abort($exception->getCode(), $exception->getMessage());
        }
    }

    public function show(Knowledge $knowledge) {
        return $knowledge;
    }

    public function preview(Knowledge $knowledge, $authId) {
        try {
            $auth = Cache::get($authId);
            if($knowledge->portal != $auth->portal) return view('errorAccess');
            return view('certification.knowledge.preview', [
                'auth' => $auth,
                'knowledge' => $knowledge
            ]);
        } catch (Exception $exception) {
            report($exception);
            abort($exception->getCode(), $exception->getMessage());
        }
    }

    public function update(Request $request, Knowledge $knowledge) {
        $data = $request->validate([
            'title' => ['required'],
            'description' => ['nullable'],
            'tags' => ['nullable'],
            'questions' => ['nullable'],
            'tests' => ['nullable'],
        ]);

        $knowledge->update($data);

        return $knowledge;
    }

    public function destroy(Knowledge $knowledge) {
        $knowledge->delete();

        return response()->json();
    }
}
