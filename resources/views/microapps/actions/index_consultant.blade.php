<x-layout_consultant>
    @push('links')
        <link href="{{asset('DataTables-1.13.4/css/dataTables.bootstrap5.css')}}" rel="stylesheet"/>
        <link href="{{asset('Responsive-2.4.1/css/responsive.bootstrap5.css')}}" rel="stylesheet"/>
    @endpush
    @push('scripts')
        <script src="{{asset('DataTables-1.13.4/js/jquery.dataTables.js')}}"></script>
        <script src="{{asset('DataTables-1.13.4/js/dataTables.bootstrap5.js')}}"></script>
        <script src="{{asset('Responsive-2.4.1/js/dataTables.responsive.js')}}"></script>
        <script src="{{asset('Responsive-2.4.1/js/responsive.bootstrap5.js')}}"></script>
        <!-- <script src="{{asset('datatable_init.js')}}"></script> -->
        <script src="{{asset('toggle_actions.js')}}"></script>
        <script src="{{asset('datatable_init_actions.js')}}"></script>
        <script>
            var actionCheckUrl = '{{ route("actions.check", ["action" => "placeholder"]) }}';
        </script>
        <script src="{{asset('check_action_status.js')}}"></script>
    @endpush
    @push('title')
        <title>Δράσεις Σχολείων</title>
    @endpush
    @php
        $user = Auth::guard('consultant')->user(); //check which user is logged in
        
        // Get all actions
        $schools = App\Models\School::whereIn('id', $user->schregion->schools->pluck('id'))->get();
        $schoolIds = $schools->pluck('id')->toArray();
        $is_supervisor = isset($is_supervisor) ? $is_supervisor : false;
        if(!$is_supervisor) {
            $actions = App\Models\microapps\Action::whereIn('school_id', $schoolIds)->get();
        } else {
            $actions = App\Models\microapps\Action::get();
            $myActions = App\Models\microapps\Action::whereIn('school_id', $schoolIds)->get();
        }
        
        //dd($is_supervisor);
        // Calculate statistics
        $totalActions = $actions->count();
        $totalTeachers = $actions->sum('number_of_teachers');
        $actionsByType = $actions->groupBy('actiontype_id');
        $actionsBySchool = $actions->groupBy('school_id');
        $actionsByStatus = $actions->groupBy('status');
    @endphp
    
    <div class="container pt-2">
        <div class="h4">Δράσεις Σχολικών Μονάδων - Στατιστικά και Παρακολούθηση</div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="container mb-4">
        <div class="row">
            <div class="col-md-3">
                <div class="card bg-primary text-white mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Συνολικές Δράσεις</h5>
                        <p class="card-text display-4">{{ $totalActions }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Συμμετέχοντες Εκπαιδευτικοί</h5>
                        <p class="card-text display-4">{{ $totalTeachers }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Σχολεία με Δράσεις</h5>
                        <p class="card-text display-4">{{ $actionsBySchool->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Ολοκληρωμένες Δράσεις</h5>
                        <p class="card-text display-4">{{ $actionsByStatus->get('completed', collect())->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Actions by Type Chart Container -->
    <div class="container mb-4">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Κατανομή Δράσεων ανά Τύπο</h5>
            </div>
            <div class="card-body">
                <canvas id="actionsByTypeChart" height="200"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Actions Table -->
    <div class="container">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Λίστα Δράσεων</h5>
            </div>
            @if($is_supervisor ?? false)
            <ul class="nav nav-tabs" id="actionsTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all-actions" type="button" role="tab">Όλες οι Δράσεις</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="mine-tab" data-bs-toggle="tab" data-bs-target="#my-actions" type="button" role="tab">Τα Σχολεία μου</button>
                </li>
            </ul>
            @endif
            <div class="card-body">
                <div class="tab-content" id="actionsTabContent">
                    <div class="tab-pane fade show active" id="all-actions" role="tabpanel">        
                        <table id="dataTable" class="small display align-middle table table-sm table-secondary table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th id="search">Σχολείο</th>
                                    <th id="search">Τίτλος Δράσης</th>
                                    <th id="search">Τύπος Δράσης</th>
                                    <th id="search">Υπεύθυνη Αρχή</th>
                                    <th id="search">Εκπαιδευτικοί</th>
                                    <th id="">Αρχεία</th>
                                    <th id="search">Κατάσταση</th>
                                    <th>Ενέργειες</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($actions as $action)
                                    <tr>
                                        <td>{{ App\Models\School::find($action->school_id)->name ?? 'Άγνωστο' }}</td>
                                        <td>{{ $action->title }}</td>
                                        <td>{{ App\Models\microapps\ActionType::find($action->actiontype_id)->name ?? 'Άγνωστο' }}</td>
                                        <td>{{ $action->implementing_authority }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $action->number_of_teachers }}</span>
                                            @if($action->teachers)
                                                <button type="button" class="btn btn-sm btn-outline-info ms-2" 
                                                        data-bs-toggle="tooltip" data-bs-placement="top" 
                                                        title="{{ $action->teachers }}">
                                                    <i class="bi bi-info-circle"></i>
                                                </button>
                                            @endif
                                        </td>
                                        <td>
                                            @if($action->records)
                                                @php
                                                    $records = explode(',', $action->records);
                                                @endphp
                                                @foreach($records as $record)
                                                    <a href="{{ asset('storage/actions/records/' . trim($record)) }}" 
                                                    class="btn btn-sm btn-outline-primary mb-1" target="_blank">
                                                        <i class="bi bi-file-earmark"></i> {{ basename(trim($record)) }}
                                                    </a><br>
                                                @endforeach
                                            @else
                                                <span class="text-muted">Δεν υπάρχουν αρχεία</span>
                                            @endif
                                        </td>
                                        <td>
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
                                        <td>
                                            <div class="btn-group">
                                                
                                                    <i class="bi bi-eye"></i>
                                                <!-- </a> -->
                                                <a href="{{ route('actions.edit', $action->id) }}" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-success action-status-toggle"
                                                        data-action-id="{{ $action->id }}" data-status="approved">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div> 
                    @if($is_supervisor ?? false)
                    <div class="tab-pane fade" id="my-actions" role="tabpanel">
                        <table id="dataTableMyActions" class="small display align-middle table table-sm table-secondary table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th id="search">Σχολείο</th>
                                    <th id="search">Τίτλος Δράσης</th>
                                    <th id="search">Τύπος Δράσης</th>
                                    <th id="search">Υπεύθυνη Αρχή</th>
                                    <th id="search">Εκπαιδευτικοί</th>
                                    <th id="">Αρχεία</th>
                                    <th id="search">Κατάσταση</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($myActions as $action)
                                    <tr>
                                        <td>{{ App\Models\School::find($action->school_id)->name ?? 'Άγνωστο' }}</td>
                                        <td>{{ $action->title }}</td>
                                        <td>{{ App\Models\microapps\ActionType::find($action->actiontype_id)->name ?? 'Άγνωστο' }}</td>
                                        <td>{{ $action->implementing_authority }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $action->number_of_teachers }}</span>
                                            @if($action->teachers)
                                                <button type="button" class="btn btn-sm btn-outline-info ms-2" 
                                                        data-bs-toggle="tooltip" data-bs-placement="top" 
                                                        title="{{ $action->teachers }}">
                                                    <i class="bi bi-info-circle"></i>
                                                </button>
                                            @endif
                                        </td>
                                        <td>
                                            @if($action->records)
                                                @php
                                                    $records = explode(',', $action->records);
                                                @endphp
                                                @foreach($records as $record)
                                                    <a href="{{ asset('storage/actions/records/' . trim($record)) }}" 
                                                    class="btn btn-sm btn-outline-primary mb-1" target="_blank">    
                                                        <i class="bi bi-file-earmark"></i> {{ basename(trim($record)) }}
                                                    </a><br>
                                                @endforeach
                                            @else
                                                <span class="text-muted">Δεν υπάρχουν αρχεία</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $statusBadge = match($action->status) {
                                                    'pending' => 'warning',
                                                    'approved' => 'success',
                                                    'rejected' => 'danger',
                                                    'completed' => 'primary',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $statusBadge }}">{{ ucfirst($action->status) }}</span>
                                        </td>
                                        
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
    <!-- Chart.js Script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Create actions by type chart
            const actionTypeCtx = document.getElementById('actionsByTypeChart').getContext('2d');
            
            @php
                $actionTypeLabels = [];
                $actionTypeCounts = [];
                //dd($actionsByType);
                foreach($actionsByType as $typeId => $actionsOfType) {
                    //print_r($actionsOfType);
                    //print_r($typeId);
                    $typeName = App\Models\microapps\ActionType::find($typeId)->description ?? "Τύπος #$typeId";
                    $actionTypeLabels[] = $typeName;
                    $actionTypeCounts[] = $actionsOfType->count();
                }
                //dd('end of foreach');
            @endphp
            
            const actionTypeLabels = @json($actionTypeLabels);
            const actionTypeCounts = @json($actionTypeCounts);
            
            new Chart(actionTypeCtx, {
                type: 'bar',
                data: {
                    labels: actionTypeLabels,
                    datasets: [{
                        label: 'Πλήθος Δράσεων',
                        data: actionTypeCounts,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-layout_consultant>