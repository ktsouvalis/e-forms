<x-layout_consultant>

@push('title')
    <title>Έλεγχος Προγραμματισμού Σχολείων</title>
@endpush

<div class="container py-4">
    <h3 class="mb-4"><i class="bi bi-calendar-check"></i> Προγραμματισμός Σχολικών Μονάδων</h3>

    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>Σχολείο</th>
                <th>Τύπος Προγραμματισμού</th>
                <th>Κατάσταση</th>
                <th>Αρχεία</th>
                <th>Ενημέρωση Συμβούλου</th>
            </tr>
        </thead>
        <tbody>
            @foreach($schools as $school)
                <tr>
                    <td>{{ $school->name }}</td>
                    <td>{{ optional($school->actionPlanning)->planning_cycle ?? '—' }}</td>
                    <td>
                        @if(optional($school->actionPlanning)->submitted)
                            <span class="badge bg-success">Υποβλήθηκε</span>
                        @else
                            <span class="badge bg-secondary">Δεν έχει υποβληθεί</span>
                        @endif
                    </td>
                    <td>
                        @php $files = optional($school->actionPlanning)->files_json; @endphp
                        @if($files)
                            @foreach($files as $category => $filesArray)
                                @foreach($filesArray as $serverFileName => $databaseFileName)
                                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded bg-light">
                                        <a href="{{ route('consultant.action_planning.download_file', ['serverFileName' => $serverFileName, 'databaseFileName' => $databaseFileName]) }}" class="btn btn-sm btn-outline-info text-dark text-start text-truncate" style="max-width: 60%;">
                                            <i class="bi bi-file-earmark-text"></i> {{ $databaseFileName }}
                                        </a>
                                    </div>
                                @endforeach
                            @endforeach
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($school->actionPlanning)
                            <form action="{{ route('consultant.action_planning.mark_seen', $school->actionPlanning->id) }}" method="POST">
                                @csrf
                                @if($school->actionPlanning->checked)
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        <span class="badge bg-info text-dark">
                                            <i class="bi bi-eye-fill"></i> Έλαβα γνώση
                                        </span>
                                        <button type="submit" class="btn btn-sm btn-link text-muted p-0">αναίρεση</button>
                                    </div>
                                @else
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Έλαβα γνώση
                                    </button>
                                @endif
                            </form>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

</x-layout_consultant>