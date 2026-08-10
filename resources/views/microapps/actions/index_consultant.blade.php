<x-layout_consultant>
    @push('links')
        <link href="{{ asset('DataTables-1.13.4/css/dataTables.bootstrap5.css') }}" rel="stylesheet">
        <link href="{{ asset('Responsive-2.4.1/css/responsive.bootstrap5.css') }}" rel="stylesheet">
        <style>
            /* ---------- Συμπαγή στατιστικά ---------- */
            .stat-tile{
                background:#f8f9fb;
                border-radius:.75rem;
                padding:.85rem 1rem;
                border-left:4px solid #dee2e6;
                height:100%;
                display:flex;
                flex-direction:column;
                transition:transform .15s ease, box-shadow .15s ease;
            }
            .stat-tile:hover{ transform:translateY(-2px); box-shadow:0 .35rem .75rem rgba(0,0,0,.07); }
            .stat-tile .stat-icon{ font-size:1rem; display:flex; align-items:center; justify-content:space-between; }
            .stat-tile .stat-value{ font-size:1.55rem; font-weight:700; line-height:1.2; }
            .stat-tile .stat-label{ font-size:.74rem; color:#6c757d; }
            .stat-tile .stat-sub{ font-size:.68rem; color:#98a1ac; }
            .stat-info-icon{ font-size:.75rem; color:#adb5bd; cursor:help; }
            .stat-info-icon:hover{ color:#0d6efd; }
            .stat-primary{ border-left-color:#0d6efd; } .stat-primary .stat-icon,.stat-primary .stat-value{ color:#0d6efd; }
            .stat-success{ border-left-color:#198754; } .stat-success .stat-icon,.stat-success .stat-value{ color:#198754; }
            .stat-info{ border-left-color:#0dcaf0; }    .stat-info .stat-icon,.stat-info .stat-value{ color:#0aa2c0; }
            .stat-warning{ border-left-color:#ffc107; } .stat-warning .stat-icon,.stat-warning .stat-value{ color:#b78a00; }

            .legend-dot{ width:.6rem; height:.6rem; border-radius:50%; display:inline-block; margin-right:.3rem; }

            .th-info-icon{ font-size:.72rem; color:#8a93a0; cursor:help; }
            .th-info-icon:hover{ color:#0d6efd; }

            /* ---------- Πίνακας σχολείων ---------- */
            #schoolsTable .school-name-cell{ cursor:pointer; min-width:260px; }
            #schoolsTable .school-name-cell:hover .school-name{ color:#0d6efd; text-decoration:underline; }
            .school-chevron{ transition:transform .2s ease; color:#adb5bd; font-size:.8rem; }
            #schoolsTable tr.shown{ background-color:rgba(13,110,253,.05) !important; }
            #schoolsTable tr.shown .school-chevron{ transform:rotate(90deg); color:#0d6efd; }
            table.dataTable > tbody > tr.child > td.child{ background:#f4f7fb; padding:.9rem 1rem; }
            .school-actions-wrapper{ border:1px solid #e3e8ef; border-radius:.6rem; background:#fff; overflow-x:auto; }
            .actions-detail-table{ margin-bottom:0; }
            .actions-detail-table thead th{ font-size:.75rem; white-space:nowrap; }

            /* ---------- Διάγραμμα ---------- */
            .chart-wrap{ position:relative; height:400px; }
            @media (max-width: 991.98px){ .chart-wrap{ height:300px; } }
        </style>
    @endpush

    @push('scripts')
        <script src="{{ asset('DataTables-1.13.4/js/jquery.dataTables.js') }}"></script>
        <script src="{{ asset('DataTables-1.13.4/js/dataTables.bootstrap5.js') }}"></script>
        <script src="{{ asset('Responsive-2.4.1/js/dataTables.responsive.js') }}"></script>
        <script src="{{ asset('Responsive-2.4.1/js/responsive.bootstrap5.js') }}"></script>
        <script src="{{ asset('toggle_actions.js') }}"></script>
        <script src="{{ asset('datatable_init_actions.js') }}"></script>
        <script>
            var actionCheckUrl = '{{ route("actions.check", ["action" => "placeholder"]) }}';
        </script>
        <script src="{{ asset('check_action_status.js') }}"></script>
    @endpush

    @push('title')
        <title>Δράσεις Σχολείων</title>
    @endpush

    @php
        $user = Auth::guard('consultant')->user();
        $schools = App\Models\School::whereIn('id', $user->schregion->schools->pluck('id'))->get();
        $schoolIds = $schools->pluck('id')->toArray();
        $is_supervisor = isset($is_supervisor) ? $is_supervisor : false;

        if (!$is_supervisor) {
            $actions = App\Models\microapps\Action::whereIn('school_id', $schoolIds)->get();
        } else {
            $actions = App\Models\microapps\Action::get();
            $schools = App\Models\School::whereIn('id', $actions->pluck('school_id')->unique())->get();
        }

        // ---------- Γενικά στατιστικά ----------
        $totalActions    = $actions->count();
        $totalTeachers   = (int) $actions->sum('number_of_teachers'); // ΣΥΜΜΕΤΟΧΕΣ, όχι μοναδικά άτομα
        $actionsByType   = $actions->groupBy('actiontype_id');
        $actionsByStatus = $actions->groupBy('status');

        $pendingCount   = $actionsByStatus->get('pending', collect())->count();
        $approvedCount  = $actionsByStatus->get('approved', collect())->count();
        $rejectedCount  = $actionsByStatus->get('rejected', collect())->count();
        $completedCount = $actionsByStatus->get('completed', collect())->count();

        $completionRate = $totalActions > 0 ? round(100 * $completedCount / $totalActions) : 0;

        // ---------- Εκτίμηση μοναδικών εκπαιδευτικών από το πεδίο ονομάτων ----------
        // (προαιρετική ένδειξη — αν το πεδίο teachers είναι κενό, δεν εμφανίζεται)
        $uniqueTeacherNames = collect();
        foreach ($actions as $action) {
            if (!empty($action->teachers)) {
                $names = preg_split('/[,;·|]+|\r\n|\n|\r/u', $action->teachers);
                foreach ($names as $name) {
                    $name = trim($name);
                    if ($name !== '') {
                        $uniqueTeacherNames->push(mb_strtolower($name, 'UTF-8'));
                    }
                }
            }
        }
        $uniqueTeacherCount = $uniqueTeacherNames->unique()->count();

        // ---------- Στατιστικά ανά σχολείο ----------
        $schoolStats = $schools->map(function ($school) use ($actions) {
            $schoolActions = $actions->where('school_id', $school->id)->values();
            $total         = $schoolActions->count();
            $completed     = $schoolActions->where('status', 'completed')->count();

            return [
                'school'    => $school,
                'actions'   => $schoolActions,
                'total'     => $total,
                'teachers'  => (int) $schoolActions->sum('number_of_teachers'), // συμμετοχές ανά σχολείο
                'pending'   => $schoolActions->where('status', 'pending')->count(),
                'approved'  => $schoolActions->where('status', 'approved')->count(),
                'completed' => $completed,
                'rejected'  => $schoolActions->where('status', 'rejected')->count(),
                'rate'      => $total > 0 ? (int) round(100 * $completed / $total) : 0,
            ];
        })->sortByDesc('total')->values();

        $activeSchools = $schoolStats->where('total', '>', 0)->count();

        // ---------- Δεδομένα διαγράμματος ανά τύπο ----------
        $actionTypeLabels = [];
        $actionTypeCounts = [];
        foreach ($actionsByType as $typeId => $actionsOfType) {
            $actionTypeLabels[] = App\Models\microapps\ActionType::find($typeId)->description ?? "Τύπος #$typeId";
            $actionTypeCounts[] = $actionsOfType->count();
        }
    @endphp

    <div class="container-fluid px-3 px-lg-4 pt-3">

        {{-- Κεφαλίδα --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <div>
                <div class="h4 mb-1">Δράσεις Σχολικών Μονάδων</div>
                <div class="text-muted small">Στατιστικά και παρακολούθηση δράσεων ανά σχολείο</div>
            </div>
            <span class="badge bg-light text-dark border shadow-sm p-2">
                <i class="bi bi-calendar3 me-1"></i> {{ now()->format('d/m/Y') }}
            </span>
        </div>

        {{-- Στατιστικά (αριστερά) + Διάγραμμα (δεξιά) --}}
        <div class="row g-3 mb-4">

            {{-- Στατιστικά --}}
            <div class="col-12 col-xl-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-speedometer2 me-2 text-primary"></i>Συγκεντρωτικά Στοιχεία</h6>
                    </div>
                    <div class="card-body d-flex flex-column gap-3">

                        {{-- Συμπαγή στατιστικά σε πλέγμα 2x2 --}}
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="stat-tile stat-primary">
                                    <div class="stat-icon"><i class="bi bi-kanban"></i></div>
                                    <div class="stat-value">{{ $totalActions }}</div>
                                    <div class="stat-label">Συνολικές Δράσεις</div>
                                </div>
                            </div>

                            {{--
                                Σημείωση: Το παρακάτω tile μετράει ΣΥΜΜΕΤΟΧΕΣ εκπαιδευτικών
                                (ένας εκπαιδευτικός σε 3 δράσεις μετράει 3 φορές).
                                Αν προτιμάτε να αφαιρεθεί εντελώς, διαγράψτε ολόκληρο
                                αυτό το <div class="col-6">...</div> block.
                            --}}
                            <div class="col-6">
                                <div class="stat-tile stat-success">
                                    <div class="stat-icon">
                                        <i class="bi bi-people"></i>
                                        <i class="bi bi-info-circle stat-info-icon"
                                           data-bs-toggle="tooltip" data-bs-placement="top"
                                           title="Πρόκειται για το σύνολο των συμμετοχών: ένας εκπαιδευτικός που συμμετέχει σε περισσότερες από μία δράσεις μετράται μία φορά σε κάθε δράση."></i>
                                    </div>
                                    <div class="stat-value">{{ $totalTeachers }}</div>
                                    <div class="stat-label">Συμμετοχές Εκπαιδευτικών</div>
                                    @if($uniqueTeacherCount > 0)
                                        <div class="stat-sub">
                                            <i class="bi bi-person-check me-1"></i>≈ {{ $uniqueTeacherCount }} μοναδικοί εκπαιδευτικοί
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="stat-tile stat-info">
                                    <div class="stat-icon"><i class="bi bi-buildings"></i></div>
                                    <div class="stat-value">{{ $activeSchools }}</div>
                                    <div class="stat-label">Σχολεία με Δράσεις</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-tile stat-warning">
                                    <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
                                    <div class="stat-value">{{ $completedCount }}</div>
                                    <div class="stat-label">Ολοκληρωμένες Δράσεις</div>
                                </div>
                            </div>
                        </div>

                        {{-- Ποσοστό ολοκλήρωσης --}}
                        <div>
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="fw-semibold">Ποσοστό Ολοκλήρωσης</span>
                                <span class="fw-bold text-primary">{{ $completionRate }}%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $completionRate }}%"></div>
                            </div>
                        </div>

                        <hr class="my-1">

                        {{-- Κατανομή κατάστασης --}}
                        <div>
                            <div class="small fw-semibold mb-2">Κατανομή ανά Κατάσταση</div>
                            <div class="progress" style="height: 10px;">
                                @if($totalActions > 0)
                                    @if($pendingCount)
                                        <div class="progress-bar bg-warning" style="width: {{ round(100 * $pendingCount / $totalActions, 1) }}%" title="Εκκρεμείς: {{ $pendingCount }}"></div>
                                    @endif
                                    @if($approvedCount)
                                        <div class="progress-bar bg-success" style="width: {{ round(100 * $approvedCount / $totalActions, 1) }}%" title="Εγκεκριμένες: {{ $approvedCount }}"></div>
                                    @endif
                                    @if($completedCount)
                                        <div class="progress-bar bg-primary" style="width: {{ round(100 * $completedCount / $totalActions, 1) }}%" title="Ολοκληρωμένες: {{ $completedCount }}"></div>
                                    @endif
                                    @if($rejectedCount)
                                        <div class="progress-bar bg-danger" style="width: {{ round(100 * $rejectedCount / $totalActions, 1) }}%" title="Απορριφθείσες: {{ $rejectedCount }}"></div>
                                    @endif
                                @else
                                    <div class="progress-bar bg-secondary" style="width: 100%"></div>
                                @endif
                            </div>
                            <div class="d-flex flex-wrap gap-3 mt-2 small text-muted">
                                <span><span class="legend-dot bg-warning"></span>Εκκρεμείς ({{ $pendingCount }})</span>
                                <span><span class="legend-dot bg-success"></span>Εγκεκριμένες ({{ $approvedCount }})</span>
                                <span><span class="legend-dot bg-primary"></span>Ολοκληρωμένες ({{ $completedCount }})</span>
                                <span><span class="legend-dot bg-danger"></span>Απορριφθείσες ({{ $rejectedCount }})</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Διάγραμμα --}}
            <div class="col-12 col-xl-8">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-bar-chart me-2 text-primary"></i>Κατανομή Δράσεων ανά Τύπο</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-wrap">
                            <canvas id="actionsByTypeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Πίνακας Σχολείων --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h6 class="mb-0">
                    <i class="bi bi-mortarboard me-2 text-primary"></i>Δράσεις ανά Σχολείο
                    @if($is_supervisor)<span class="badge bg-secondary ms-1">Όλα τα σχολεία</span>@endif
                </h6>
                <span class="badge bg-light text-dark border">{{ $schoolStats->count() }} Σχολεία</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="schoolsTable" class="table table-hover align-middle small w-100">
                        <thead>
                            <tr>
                                <th>Σχολείο</th>
                                <th class="text-center">Δράσεις</th>
                                <th class="text-center">
                                    Συμμετοχές Εκπ/κών
                                    <i class="bi bi-info-circle th-info-icon ms-1"
                                       data-bs-toggle="tooltip" data-bs-placement="top"
                                       title="Σύνολο συμμετοχών εκπαιδευτικών στις δράσεις του σχολείου. Ένας εκπαιδευτικός που συμμετέχει σε πολλαπλές δράσεις μετράται μία φορά σε κάθε δράση."></i>
                                </th>
                                <th class="text-center">Εκκρεμείς</th>
                                <th class="text-center">Εγκεκριμένες</th>
                                <th class="text-center">Ολοκληρωμένες</th>
                                <th style="min-width:150px;">Ολοκλήρωση</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($schoolStats as $stat)
                                @php $school = $stat['school']; @endphp
                                <tr class="school-row" data-school-id="{{ $school->id }}">
                                    <td class="school-name-cell">
                                        <i class="bi bi-chevron-right school-chevron me-2"></i>
                                        <span class="fw-semibold school-name">{{ $school->name }}</span>
                                        @if($stat['total'] == 0)
                                            <span class="badge bg-light text-muted border ms-2">Καμία δράση</span>
                                        @endif
                                    </td>
                                    <td class="text-center" data-order="{{ $stat['total'] }}">
                                        <span class="badge bg-primary rounded-pill">{{ $stat['total'] }}</span>
                                    </td>
                                    <td class="text-center" data-order="{{ $stat['teachers'] }}">
                                        {{ $stat['teachers'] ?: '—' }}
                                    </td>
                                    <td class="text-center" data-order="{{ $stat['pending'] }}">
                                        @if($stat['pending'] > 0)
                                            <span class="badge bg-warning text-dark">{{ $stat['pending'] }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center" data-order="{{ $stat['approved'] }}">
                                        @if($stat['approved'] > 0)
                                            <span class="badge bg-success">{{ $stat['approved'] }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center" data-order="{{ $stat['completed'] }}">
                                        @if($stat['completed'] > 0)
                                            <span class="badge bg-primary">{{ $stat['completed'] }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td data-order="{{ $stat['rate'] }}">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px; min-width: 70px;">
                                                <div class="progress-bar {{ $stat['rate'] == 100 ? 'bg-success' : 'bg-primary' }}" style="width: {{ $stat['rate'] }}%"></div>
                                            </div>
                                            <span class="small text-muted text-end" style="min-width: 34px;">{{ $stat['rate'] }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="text-muted small mt-2">
                    <i class="bi bi-info-circle me-1"></i>Κάντε κλικ στο όνομα ενός σχολείου για να δείτε αναλυτικά τις δράσεις του.
                </div>
            </div>
        </div>
    </div>

    {{-- Κρυφά templates με τις αναλυτικές δράσεις κάθε σχολείου --}}
    @foreach($schoolStats as $stat)
        <template id="school-actions-tpl-{{ $stat['school']->id }}">
            <div class="school-actions-wrapper p-2">
                @if($stat['actions']->isEmpty())
                    <div class="text-muted small p-3 mb-0">
                        <i class="bi bi-inbox me-1"></i>Δεν υπάρχουν καταχωρημένες δράσεις για αυτό το σχολείο.
                    </div>
                @else
                    <table class="table table-sm table-striped table-bordered small mb-0 actions-detail-table">
                        <thead class="table-light">
                            <tr>
                                <th>Τίτλος Δράσης</th>
                                <th>Τύπος Δράσης</th>
                                <th>Υπεύθυνη Αρχή</th>
                                <th class="text-center">Εκπαιδευτικοί</th>
                                <th>Αρχεία</th>
                                <th class="text-center">Κατάσταση</th>
                                <th class="text-center">Ενέργειες</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stat['actions'] as $action)
                                <tr>
                                    <td>{{ $action->title }}</td>
                                    <td>{{ App\Models\microapps\ActionType::find($action->actiontype_id)->name ?? 'Άγνωστο' }}</td>
                                    <td>{{ $action->implementing_authority }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $action->number_of_teachers }}</span>
                                        @if($action->teachers)
                                            <button type="button" class="btn btn-sm btn-outline-info ms-1"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ $action->teachers }}">
                                                <i class="bi bi-info-circle"></i>
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        @if($action->records)
                                            @foreach(explode(',', $action->records) as $record)
                                                <a href="{{ asset('storage/actions/records/' . trim($record)) }}"
                                                   class="btn btn-sm btn-outline-primary mb-1 me-1" target="_blank">
                                                    <i class="bi bi-file-earmark"></i> {{ basename(trim($record)) }}
                                                </a>
                                            @endforeach
                                        @else
                                            <span class="text-muted">Δεν υπάρχουν αρχεία</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $statusBadge = match($action->status) {
                                                'pending' => 'warning',
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                                'completed' => 'primary',
                                                default => 'secondary'
                                            };
                                            $statusText = match($action->status) {
                                                'pending' => 'Εκκρεμεί',
                                                'approved' => 'Εγκρίθηκε',
                                                'rejected' => 'Απορρίφθηκε',
                                                'completed' => 'Ολοκληρώθηκε',
                                                default => 'Δεν ορίστηκε'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $statusBadge }}">{{ $statusText }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('actions.edit', $action->id) }}" class="btn btn-warning" title="Επεξεργασία">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-success action-status-toggle"
                                                    data-action-id="{{ $action->id }}" data-status="approved" title="Έγκριση">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </template>
    @endforeach

    {{-- Chart.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ---------- Tooltips ----------
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
                new bootstrap.Tooltip(el);
            });

            // ---------- Πίνακας Σχολείων ----------
            if (window.jQuery && jQuery.fn.DataTable) {
                var schoolsTable = jQuery('#schoolsTable').DataTable({
                    order: [[1, 'desc']],
                    pageLength: 15,
                    autoWidth: false,
                    language: {
                        search: 'Αναζήτηση:',
                        lengthMenu: 'Προβολή _MENU_ σχολείων',
                        info: 'Εμφάνιση _START_ - _END_ από _TOTAL_ σχολεία',
                        infoEmpty: 'Δεν υπάρχουν σχολεία',
                        infoFiltered: '(φιλτράρισμα από _MAX_ συνολικά)',
                        zeroRecords: 'Δεν βρέθηκαν σχολεία',
                        emptyTable: 'Δεν υπάρχουν δεδομένα στον πίνακα',
                        paginate: {
                            first: 'Πρώτη',
                            last: 'Τελευταία',
                            next: '<i class="bi bi-chevron-right"></i>',
                            previous: '<i class="bi bi-chevron-left"></i>'
                        }
                    }
                });

                // Κλικ στο όνομα του σχολείου -> εμφάνιση/απόκρυψη αναλυτικών δράσεων
                jQuery('#schoolsTable tbody').on('click', 'td.school-name-cell', function () {
                    var tr = jQuery(this).closest('tr');
                    if (!tr.hasClass('school-row')) return;

                    var row = schoolsTable.row(tr);
                    var schoolId = tr.attr('data-school-id');

                    if (row.child.isShown()) {
                        row.child.hide();
                        tr.removeClass('shown');
                    } else {
                        var tpl = document.getElementById('school-actions-tpl-' + schoolId);
                        var content = tpl
                            ? tpl.innerHTML
                            : '<div class="p-3 text-muted small">Δεν βρέθηκαν δεδομένα.</div>';

                        row.child(content).show();
                        tr.addClass('shown');

                        // Ενεργοποίηση tooltips μέσα στο expanded περιεχόμενο
                        jQuery(row.child()).find('[data-bs-toggle="tooltip"]').each(function () {
                            new bootstrap.Tooltip(this);
                        });
                    }
                });
            }

            // ---------- Διάγραμμα ανά τύπο δράσης ----------
            var actionTypeCtx = document.getElementById('actionsByTypeChart').getContext('2d');
            var actionTypeLabels = @json($actionTypeLabels);
            var actionTypeCounts = @json($actionTypeCounts);

            var bgPalette = [
                'rgba(13, 110, 253, .65)', 'rgba(25, 135, 84, .65)', 'rgba(13, 202, 240, .65)',
                'rgba(255, 193, 7, .7)',   'rgba(220, 53, 69, .65)', 'rgba(111, 66, 193, .65)',
                'rgba(253, 126, 20, .65)', 'rgba(32, 201, 151, .65)','rgba(214, 51, 132, .65)',
                'rgba(108, 117, 125, .65)'
            ];
            var borderPalette = [
                'rgba(13, 110, 253, 1)', 'rgba(25, 135, 84, 1)', 'rgba(13, 202, 240, 1)',
                'rgba(255, 193, 7, 1)',  'rgba(220, 53, 69, 1)', 'rgba(111, 66, 193, 1)',
                'rgba(253, 126, 20, 1)', 'rgba(32, 201, 151, 1)','rgba(214, 51, 132, 1)',
                'rgba(108, 117, 125, 1)'
            ];

            new Chart(actionTypeCtx, {
                type: 'bar',
                data: {
                    labels: actionTypeLabels,
                    datasets: [{
                        label: 'Πλήθος Δράσεων',
                        data: actionTypeCounts,
                        backgroundColor: actionTypeLabels.map(function (_, i) { return bgPalette[i % bgPalette.length]; }),
                        borderColor: actionTypeLabels.map(function (_, i) { return borderPalette[i % borderPalette.length]; }),
                        borderWidth: 1.5,
                        borderRadius: 6,
                        maxBarThickness: 42
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (ctx) { return ' ' + ctx.parsed.x + ' δράσεις'; }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { precision: 0 },
                            grid: { color: 'rgba(0,0,0,.05)' }
                        },
                        y: {
                            grid: { display: false }
                        }
                    }
                }
            });
        });
    </script>
</x-layout_consultant>