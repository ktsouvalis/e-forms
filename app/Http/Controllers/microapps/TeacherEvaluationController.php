<?php

namespace App\Http\Controllers\microapps;

use App\Models\Teacher;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\microapps\EvaluationConsultantSnapshot;

class TeacherEvaluationController extends Controller
{
    public function index()
    {
        $consultant = Auth::guard('consultant')->user();
        $afm = $consultant->afm;

        $response = Http::withHeaders([
            'X-API-Key' => 'mysecretapikey',
        ])->get(env('E_DIRECTORATE').'/Evaluation/consultantcurrentlist', [
            'afm' => $consultant->afm,
        ]);
        $evaluations = collect($response->successful() ? $response->json() : [])
            ->sort(function ($a, $b) use ($afm) {
                $aMine = ($a['A1EvaluatorAFM'] ?? null) === $afm ? 0 : 1;
                $bMine = ($b['A1EvaluatorAFM'] ?? null) === $afm ? 0 : 1;

                return [$aMine, $a['LastName'] ?? '', $a['FirstName'] ?? '']
                <=> [$bMine, $b['LastName'] ?? '', $b['FirstName'] ?? ''];
            })
            ->values()
            ->all();
        
        $consultant = auth('consultant')->user();
        $snapshot   = EvaluationConsultantSnapshot::firstWhere('consultant_id', $consultant->id);
        $lastVisit  = $snapshot?->updated_at;

        $evaluatorOf = fn ($r, $s) => implode(' / ', array_filter([
            $r[$s.'Evaluator'] ?? null,
            $s === 'B' ? ($r['BEvaluator2'] ?? null) : null,
        ]));

        $added = []; $removed = []; $changed = [];

        if ($snapshot) {
            $previous = collect($snapshot->data)->keyBy('EvaluationId');
            $current  = collect($evaluations)->keyBy('EvaluationId');

            foreach ($current as $id => $row) {
                if (!$previous->has($id)) {
                    $added[] = $id;
                    continue;
                }
                $old = $previous[$id];
                foreach (['A1', 'A2', 'B'] as $s) {
                    $parts = [];
                    if (($row[$s.'StatusId'] ?? null) !== ($old[$s.'StatusId'] ?? null)) {
                        $parts[] = ($old[$s.'StatusName'] ?? '—').' → '.($row[$s.'StatusName'] ?? '—');
                    }
                    $oldEv = $evaluatorOf($old, $s);
                    $newEv = $evaluatorOf($row, $s);
                    if ($oldEv !== $newEv) {
                        $parts[] = 'αξιολογητής: '.($oldEv ?: '—').' → '.($newEv ?: '—');
                    }
                    if ($parts) {
                        $changed[$id][$s] = implode(', ', $parts);
                    }
                }
            }

            $removed = $previous->diffKeys($current)->values()->all();
        }

        EvaluationConsultantSnapshot::updateOrCreate(
            ['consultant_id' => $consultant->id],
            ['data' => $evaluations]
        )->touch();

        $teachers = Teacher::whereIn('afm', collect($evaluations)->pluck('AFM')->filter())
            ->get(['afm', 'telephone', 'mail', 'sch_mail'])
            ->keyBy('afm');

        return view('consultants.teacher_evaluation.index', compact(
            'evaluations', 'teachers', 'added', 'removed', 'changed', 'lastVisit'
        ));

    }
}
