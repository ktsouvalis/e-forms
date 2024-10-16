<x-layout>
    @push('links')
        <link href="{{ asset('DataTables-1.13.4/css/dataTables.bootstrap5.css') }}" rel="stylesheet"/>
        <link href="{{ asset('Responsive-2.4.1/css/responsive.bootstrap5.css') }}" rel="stylesheet"/>
    @endpush

    @push('scripts')
        <script src="{{ asset('DataTables-1.13.4/js/jquery.dataTables.js') }}"></script>
        <script src="{{ asset('DataTables-1.13.4/js/dataTables.bootstrap5.js') }}"></script>
        <script src="{{ asset('Responsive-2.4.1/js/dataTables.responsive.js') }}"></script>
        <script src="{{ asset('Responsive-2.4.1/js/responsive.bootstrap5.js') }}"></script>
        <script src="{{ asset('datatable_init.js') }}"></script>
    @endpush
    @push('title')
        <title>Αριθμητικά Στοιχεία Δημοτικών για Προγραμματισμό 2024-25</title>
    @endpush
    @php
        // $plans = App\Models\microapps\EnrollmentsClasses::with('enrollment', 'enrollment.school')->get();
         $plans = App\Models\microapps\EnrollmentsClasses::with('enrollment', 'enrollment.school')
            ->whereHas('enrollment.school', function ($query) {
                $query->where('primary', 1);
            })
            ->get();
        $schools_not_having_planning = App\Models\School::where('primary',1)->where('is_active',1)->whereDoesntHave('enrollments.enrollmentClasses')->get();
        $schoolCount = 0;
    @endphp
    <div class="h4">
        Αριθμητικά Στοιχεία Δημοτικών για Προγραμματισμό 2024-25
    </div>
    <div class="table-responsive">
    <table id="dataTable" class="small text-center display table table-sm table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th id="search">Κωδικός Σχολείου</th>
                <th id="search">Σχολείο</th>
                <th id="search">Οργανικότητα</th>
                <th id="search">Λειτουργικότητα</th>
                <th>Μαθ. Α</th>
                <th>Τμ. Α</th>
                <th>Μαθ. Β</th>
                <th>Τμ. Β</th>
                <th>Μαθ. Γ</th>
                <th>Τμ. Γ</th>
                <th>Μαθ. Δ</th>
                <th>Τμ. Δ</th>
                <th>Μαθ. Ε</th>
                <th>Τμ. Ε</th>
                <th>Μαθ. ΣΤ</th>
                <th>Τμ. ΣΤ</th>
                <th id="">Μαθητές Πρωινής Ζώνης</th>
                <th id="">Τμήματα Πρωινής ζώνης</th>
                <th id="">Μαθητές Ολοήμερου Ζ1</th>
                <th id="">Τμήματα Ζ1</th>
                <th id="">Μαθητές Ολοήμερου Ζ2</th>
                <th id="">Τμήματα Ζ2</th>
                <th id="">Μαθητές Ολοήμερου Ζ3</th>
                <th id="">Τμήματα Ζ3</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($plans as $plan)
            {{-- @if($plan->enrollment->school->primary) --}}
                <tr>
                    <td>{{ $plan->enrollment->school->code }} </td>
                    <td>{{ $plan->enrollment->school->name }} </td>
                    <td>{{ $plan->enrollment->school->organikotita }}</td>
                    <td>{{ $plan->enrollment->school->leitourgikotita }}</td>
                    @php
                        $morning_classes_json = $plan->morning_classes;
                       
                        if($morning_classes_json){
                            $morning_classes = json_decode($morning_classes_json);
                                for($i=0; $i<6; $i++){
                                    if(isset($morning_classes[$i]->nr_of_students)){
                                        echo '<td>'.$morning_classes[$i]->nr_of_students.'</td><td>'.$morning_classes[$i]->nr_of_sections.'</td>';
                                    } else {
                                        echo '<td></td><td></td>';
                                    }
                                }
                        }
                        else{
                            echo '<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>';
                        }
                    @endphp
                    
                    @php
                        $morning_zone_classes_json = $plan->morning_zone_classes;
                        if($morning_zone_classes_json){
                            $morning_zone_classes = json_decode($morning_zone_classes_json);
                            echo '<td>'.$morning_zone_classes[0]->nr_of_students.'</td><td>'.$morning_zone_classes[0]->nr_of_sections.'</td>';        
                        } else {
                            echo '<td></td><td></td>';
                        }
                    @endphp
                    {{-- Ολοήμερο --}}
                    @php
                    $all_day_school_classes_json = $plan->all_day_school_classes;
                    if($all_day_school_classes_json){
                        $all_day_school_classes = json_decode($all_day_school_classes_json);
                        for($i=0; $i<3; $i++){
                            if(isset($all_day_school_classes[$i]->nr_of_students)){
                                echo '<td>'.$all_day_school_classes[$i]->nr_of_students.'</td><td>'.$all_day_school_classes[$i]->nr_of_sections.'</td>';
                            } else {
                                echo '<td></td><td></td>';
                            }
                            
                        }
                       
                    }
                    else{
                        echo '<td></td><td></td><td></td><td></td><td></td><td></td>';
                    }
                    @endphp
                </tr> 
            {{-- @endif   --}}
            @endforeach
        </tbody>
    </table>
    </div>


    <div class="my-3"><div class="h5">Σχολεία που δεν έχουν υποβάλλει</div>
    <div class="table-responsive">
        <table class="table table-bordered">
            <tr>
                <th>AA</th>
                <th id="search">Σχολείο</th>
                <th id="search">mail</th>
            </tr>
            @foreach ($schools_not_having_planning as $school)
                @if($school->public == 1 && $school->special_needs == 0 && $school->experimental == 0)
                <tr>
                    <td> {{ ++$schoolCount }} </td>
                    <td>{{ $school->name }}</td>
                    <td>{{ $school->mail }}</td>
                </tr>
                @endif
            @endforeach
        </table>
    </div>
    </div>
</x-layout>