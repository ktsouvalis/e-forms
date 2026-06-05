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
        <title>Αριθμητικά Στοιχεία Δημοτικών για Προγραμματισμό 2026-27</title>
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
        Αριθμητικά Στοιχεία Δημοτικών για Προγραμματισμό 2026-27
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
                <th>Παρατηρήσεις Πρωινού</th>
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
                    {{-- 1-4 --}}
                    <td>{{ $plan->enrollment->school->code }}</td>
                    <td>{{ $plan->enrollment->school->name }}</td>
                    <td>{{ $plan->enrollment->school->organikotita }}</td>
                    <td>{{ $plan->enrollment->school->leitourgikotita }}</td>

                    {{-- 5-16: Grades A through ΣΤ --}}
                    @php
                        $grade_classes = json_decode($plan->grade_classes ?? '[]');
                    @endphp
                    @for ($i = 0; $i < 6; $i++)
                        <td>{{ $grade_classes[$i]->nr_of_students ?? '' }}</td>
                        <td>{{ $grade_classes[$i]->nr_of_sections ?? '' }}</td>
                    @endfor

                    {{-- 17: Παρατηρήσεις Πρωινού --}}
                    <td class="text-start">{!! $plan->comments ?: '—' !!}</td>

                    {{-- 18-19: Πρωινή Ζώνη --}}
                    @php
                        $morning = json_decode($plan->morning_zone_classes ?? '[]');
                    @endphp
                    <td>{{ $morning[0]->nr_of_students ?? '' }}</td>
                    <td>{{ $morning[0]->nr_of_sections ?? '' }}</td>

                    {{-- 20-25: Ολοήμερο Ζ1, Ζ2, Ζ3 --}}
                    @php
                        $allday = json_decode($plan->all_day_school_classes ?? '[]');
                    @endphp
                    @for ($i = 0; $i < 3; $i++)
                        <td>{{ $allday[$i]->nr_of_students ?? '' }}</td>
                        <td>{{ $allday[$i]->nr_of_sections ?? '' }}</td>
                    @endfor
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