<x-layout_consultant>
    @push('title')
        <title>Αξιολόγηση Εκπαιδευτικών</title>
    @endpush

    @push('links')
        <link href="{{ asset('DataTables-1.13.4/css/dataTables.bootstrap5.css') }}" rel="stylesheet"/>
        <style>
            #evaluationsTable.table > :not(caption) > * > * {
                padding: .15rem .35rem;
                line-height: 1.2;
            }
            #evaluationsTable .filters input {
                height: 1.4rem;
                font-size: .75rem;
                padding: 0 .15rem;
            }
            #evaluationsTable td,
            #evaluationsTable th {
                white-space: nowrap;
            }
        </style>
    @endpush

    @php
        $stageLabels = ['A1' => 'Α1', 'A2' => 'Α2', 'B' => 'Β'];
        $byId = collect($evaluations)->keyBy('EvaluationId');
    @endphp

    <div class="container my-3" style="max-width: 1200px;">

        {{-- Επικεφαλίδα --}}
        <div class="d-flex justify-content-between align-items-baseline mb-2">
            <h5 class="mb-0">
                Αξιολόγηση Εκπαιδευτικών
                <span class="badge bg-secondary align-middle">{{ count($evaluations) }}</span>
            </h5>
            @if($lastVisit)
                <small class="text-muted">Τελευταία επίσκεψη: {{ $lastVisit->format('d/m/Y H:i') }}</small>
            @endif
        </div>

        {{-- Αλλαγές --}}
        @if($lastVisit)
            @if($added || $changed || $removed)
                <div class="card border-warning mb-3">
                    <div class="card-header py-1 small fw-bold bg-warning-subtle">
                        Αλλαγές από την τελευταία επίσκεψη
                    </div>
                    <ul class="list-group list-group-flush small">
                        @foreach($added as $id)
                            @php $r = $byId[$id]; @endphp
                            <li class="list-group-item py-1">
                                <span class="badge bg-success me-1">Προστέθηκε</span>
                                <strong>{{ $r['LastName'] }} {{ $r['FirstName'] }}</strong>
                                – {{ $r['ServiceSchoolName'] }} ({{ $r['EvaluationCategoryName'] }})
                            </li>
                        @endforeach

                        @foreach($changed as $id => $stages)
                            @php $r = $byId[$id]; @endphp
                            <li class="list-group-item py-1">
                                <span class="badge bg-warning text-dark me-1">Αλλαγή</span>
                                <strong>{{ $r['LastName'] }} {{ $r['FirstName'] }}</strong>
                                @foreach($stages as $s => $text)
                                    <span class="ms-2"><u>{{ $stageLabels[$s] }}</u>: {{ $text }}</span>
                                @endforeach
                            </li>
                        @endforeach

                        @foreach($removed as $r)
                            <li class="list-group-item py-1">
                                <span class="badge bg-danger me-1">Αφαιρέθηκε</span>
                                <strong>{{ $r['LastName'] }} {{ $r['FirstName'] }}</strong>
                                – {{ $r['ServiceSchoolName'] }} ({{ $r['EvaluationCategoryName'] }})
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <p class="small text-muted mb-2">Καμία αλλαγή από την τελευταία επίσκεψη.</p>
            @endif
        @endif

        {{-- Πίνακας --}}
        @if(empty($evaluations))
            <p>Δεν βρέθηκαν εγγραφές.</p>
        @else
            <div class="card">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table id="evaluationsTable" class="table table-sm table-hover align-middle small mb-0 w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>Επώνυμο</th>
                                    <th>Όνομα</th>
                                    <th>Κλάδος</th>
                                    <th>Σχολείο Υπηρέτησης</th>
                                    <th>Κατηγορία</th>
                                    <th>Διορισμός</th>
                                    <th>Δ/ντής</th>
                                    <th>Α1</th>
                                    <th>Α2</th>
                                    <th>Β</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($evaluations as $row)
                                    @php
                                        $t = $teachers[$row['AFM']] ?? null;
                                        $details = [
                                            'ΑΜ'                    => $row['AM'],
                                            'ΑΦΜ'                   => $row['AFM'],
                                            'Κλάδος'                => $row['Branch'],
                                            'Σχολείο Υπηρέτησης'    => $row['ServiceSchoolName'],
                                            'Διεύθυνση Οργανικής'   => $row['OrganicDirectorateName'],
                                            'Σχολείο Οργανικής'     => $row['OrganicSchoolName'],
                                            'Κατηγορία Αξιολόγησης' => $row['EvaluationCategoryName'],
                                            'Ημ. Διορισμού'         => $row['AppointmentDate'] ? \Carbon\Carbon::parse($row['AppointmentDate'])->format('d/m/Y') : null,
                                            'Διευθυντής'            => $row['IsDirector'] ? 'Ναι' : 'Όχι',
                                            'Υποδιευθυντής'         => $row['IsSubdirector'] ? 'Ναι' : 'Όχι',
                                            'Εκκρεμή Στάδια'        => $row['PendingStagesNum'],
                                            'Σχόλιο Αξιολόγησης'    => $row['EvaluationComment'],
                                            'Τηλέφωνο'              => $t?->telephone,
                                            'Email'                 => $t?->mail,
                                            'Σχολικό Email'         => $t?->sch_mail,
                                        ];
                                    @endphp
                                    <tr class="{{ in_array($row['EvaluationId'], $added) ? 'table-success' : '' }}">
                                        <td>
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#teacherModal"
                                               data-title="{{ $row['LastName'] }} {{ $row['FirstName'] }}"
                                               data-details="{{ json_encode($details) }}">
                                                {{ $row['LastName'] }}
                                            </a>
                                        </td>
                                        <td>{{ $row['FirstName'] }}</td>
                                        <td>{{ $row['Branch'] }}</td>
                                        <td>
                                            <span class="d-inline-block text-truncate align-bottom" style="max-width: 220px;" title="{{ $row['ServiceSchoolName'] }}">
                                                {{ $row['ServiceSchoolName'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="d-inline-block text-truncate align-bottom" style="max-width: 130px;" title="{{ $row['EvaluationCategoryName'] }}">
                                                {{ $row['EvaluationCategoryName'] }}
                                            </span>
                                        </td>
                                        <td>{{ $row['AppointmentDate'] ? \Carbon\Carbon::parse($row['AppointmentDate'])->format('d/m/Y') : '—' }}</td>
                                        <td>{{ $row['IsDirector'] ? 'Ναι' : 'Όχι' }}</td>

                                        @foreach($stageLabels as $stage => $label)
                                            @php
                                                $evaluator = implode(' / ', array_filter([
                                                    $row[$stage.'Evaluator'],
                                                    $stage === 'B' ? $row['BEvaluator2'] : null,
                                                ]));
                                            @endphp
                                            <td class="{{ isset($changed[$row['EvaluationId']][$stage]) ? 'table-warning' : '' }}">
                                                @if($row[$stage.'StatusId'] == 1)
                                                    <span class="d-inline-block text-truncate align-bottom text-muted" style="max-width: 140px;" title="{{ $evaluator }}">
                                                        {{ $evaluator ?: $row[$stage.'StatusName'] }}
                                                    </span>
                                                @elseif($row[$stage.'StatusId'] == 7)
                                                    {{ $row[$stage.'StatusName'] }}
                                                @else
                                                    <a href="#" data-bs-toggle="modal" data-bs-target="#stageModal"
                                                       data-title="Στάδιο {{ $label }} – {{ $row['LastName'] }} {{ $row['FirstName'] }}"
                                                       data-evaluator="{{ $evaluator ?: '—' }}"
                                                       data-date="{{ $row[$stage.'Date'] ? \Carbon\Carbon::parse($row[$stage.'Date'])->format('d/m/Y') : '—' }}"
                                                       data-comment="{{ $row[$stage.'Comment'] ?: '—' }}">
                                                        {{ $row[$stage.'StatusName'] }}
                                                    </a>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Modal σταδίου --}}
    <div class="modal fade" id="stageModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h6 class="modal-title" id="stageModalTitle"></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body small">
                    <p class="mb-1"><strong>Αξιολογητής:</strong> <span id="stageModalEvaluator"></span></p>
                    <p class="mb-1"><strong>Ημερομηνία:</strong> <span id="stageModalDate"></span></p>
                    <p class="mb-1"><strong>Σχόλια:</strong></p>
                    <p id="stageModalComment" class="mb-0" style="white-space: pre-line;"></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal εκπαιδευτικού --}}
    <div class="modal fade" id="teacherModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h6 class="modal-title" id="teacherModalTitle"></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-sm small mb-0">
                        <tbody id="teacherModalBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('DataTables-1.13.4/js/jquery.dataTables.js') }}"></script>
        <script src="{{ asset('DataTables-1.13.4/js/dataTables.bootstrap5.js') }}"></script>
        <script>
            $(function () {
                $('#evaluationsTable thead tr').clone().addClass('filters').appendTo('#evaluationsTable thead');

                $('#evaluationsTable').DataTable({
                    order: [],
                    orderCellsTop: true,
                    pageLength: 25,
                    initComplete: function () {
                        var api = this.api();
                        api.columns().every(function (colIdx) {
                            var cell = $('#evaluationsTable .filters th').eq(colIdx);
                            cell.html('<input type="text" class="form-control form-control-sm py-0" placeholder="…">');
                            $('input', cell).on('keyup change', function () {
                                if (api.column(colIdx).search() !== this.value) {
                                    api.column(colIdx).search(this.value).draw();
                                }
                            });
                        });
                    }
                });

                $('#stageModal').on('show.bs.modal', function (e) {
                    var link = $(e.relatedTarget);
                    $('#stageModalTitle').text(link.data('title'));
                    $('#stageModalEvaluator').text(link.data('evaluator'));
                    $('#stageModalDate').text(link.data('date'));
                    $('#stageModalComment').text(link.data('comment'));
                });

                $('#teacherModal').on('show.bs.modal', function (e) {
                    var link = $(e.relatedTarget);
                    $('#teacherModalTitle').text(link.data('title'));
                    var body = $('#teacherModalBody').empty();
                    $.each(link.data('details'), function (label, value) {
                        body.append(
                            $('<tr>').append(
                                $('<th style="width:35%">').text(label),
                                $('<td style="white-space:pre-line">').text(value === null || value === '' ? '—' : value)
                            )
                        );
                    });
                });
            });
        </script>
    @endpush
</x-layout_consultant>