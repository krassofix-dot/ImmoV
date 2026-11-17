<?php

namespace App\Modules\System\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use OwenIt\Auditing\Models\Audit;

class AuditController extends Controller
{
    public const TYPES = ['user', 'country', 'property', 'building', 'unit', 'party', 'occupancy'];

    public function index(Request $request): View
    {
        $audits = Audit::query()
            ->with('user')
            ->when($request->string('type')->value(), fn ($q, $t) => $q->where('auditable_type', $t))
            ->when($request->string('event')->value(), fn ($q, $e) => $q->where('event', $e))
            ->when($request->integer('user'), fn ($q, $u) => $q->where('user_type', 'user')->where('user_id', $u))
            ->when($request->date('from'), fn ($q, $d) => $q->where('created_at', '>=', $d->startOfDay()))
            ->when($request->date('to'), fn ($q, $d) => $q->where('created_at', '<=', $d->endOfDay()))
            ->latest('id')
            ->paginate(50)->withQueryString();

        return view('system.audits', [
            'audits' => $audits,
            'labels' => self::subjectLabels($audits->getCollection()),
            'types' => self::TYPES,
            'events' => Audit::query()->distinct()->orderBy('event')->pluck('event'),
            'users' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Bezeichnungen der protokollierten Datensätze, auch wenn sie inzwischen
     * gelöscht sind. Schlüssel: "<typ>:<id>".
     *
     * @return array<string, string>
     */
    public static function subjectLabels(Collection $audits): array
    {
        $labels = [];
        foreach ($audits->groupBy('auditable_type') as $type => $group) {
            $class = Relation::getMorphedModel($type);
            if ($class === null) {
                continue;
            }
            $query = $class::query();
            if (in_array(SoftDeletes::class, class_uses_recursive($class), true)) {
                $query->withTrashed();
            }
            if ($type === 'occupancy') {
                $query->with(['unit' => fn ($q) => $q->withTrashed(), 'party' => fn ($q) => $q->withTrashed()]);
            }
            foreach ($query->whereKey($group->pluck('auditable_id')->unique())->get() as $model) {
                $labels[$type.':'.$model->getKey()] = match ($type) {
                    'occupancy' => trim(($model->unit?->name ?? '?').' · '.($model->party?->display_name ?? $model->type->label())),
                    default => (string) ($model->display_name ?? $model->name ?? $model->code ?? '#'.$model->getKey()),
                };
            }
        }

        return $labels;
    }
}
