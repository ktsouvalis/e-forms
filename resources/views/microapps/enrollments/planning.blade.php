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
        <title>Προγραμματισμός 2024-25</title>
    @endpush
    @php
        $plans = App\Models\microapps\EnrollmentsClasses::with('enrollment', 'enrollment.school')->get();
    @endphp
    <div class="table-responsive">
    <table id="dataTable" class="small text-center display table table-sm table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th id="search">Σχολείο</th>
                <th id="search">Οργανικότητα</th>
                <th id="search">Λειτουργικότητα</th>
                <th id="">Νέα Λειτουργικότητα</th>
                <th id="">Τμήματα</th>
                <th id="">Τμήματα Πρωινής ζώνης</th>
                <th id="">Ολοήμερο Ζ1</th>
                <th id="">Ολοήμερο Ζ2</th>
                <th id="">Διευρυμένο Ολοήμερο</th>
                <th id="search">Κωδικός Σχολείου</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($plans as $plan)
                <tr>
                    <td>{{ $plan->enrollment->school->name }}</td>
                    <td>{{ $plan->enrollment->school->organikotita }}</td>
                    <td>{{ $plan->enrollment->school->leitourgikotita }}</td>
                    @php
                        $kostas = 5;
                        $morning_classes_string = '';
                        $morning_classes_json = $plan->morning_classes;
                        $total_sections_number = 0;
                        if($morning_classes_json){
                            $morning_classes = json_decode($morning_classes_json);
                            if($plan->enrollment->school->leitourgikotita >= 6){
                                
                                $morning_classes_string = 'A:'.$morning_classes[0]->nr_of_students.' <strong>'.$morning_classes[0]->nr_of_sections.'</strong> '.optional($morning_classes[0])->comment.'<br>';
                                $morning_classes_string .= 'B:'.$morning_classes[1]->nr_of_students.' <strong>'.$morning_classes[1]->nr_of_sections.'</strong> '.optional($morning_classes[1])->comment.'<br>';
                                $morning_classes_string .= 'Γ:'.$morning_classes[2]->nr_of_students.' <strong>'.$morning_classes[2]->nr_of_sections.'</strong> '.optional($morning_classes[2])->comment.'<br>';
                                $morning_classes_string .= 'Δ:'.$morning_classes[3]->nr_of_students.' <strong>'.$morning_classes[3]->nr_of_sections.'</strong> '.optional($morning_classes[3])->comment.'<br>';
                                $morning_classes_string .= 'Ε:'.$morning_classes[4]->nr_of_students.' <strong>'.$morning_classes[4]->nr_of_sections.'</strong> '.optional($morning_classes[4])->comment.'<br>';
                                $morning_classes_string .= 'ΣΤ:'.$morning_classes[5]->nr_of_students.' <strong>'.$morning_classes[5]->nr_of_sections.'</strong> '.optional($morning_classes[5])->comment.'<br>';
                                $total_sections_number = $morning_classes[0]->nr_of_sections + $morning_classes[1]->nr_of_sections + $morning_classes[2]->nr_of_sections + $morning_classes[3]->nr_of_sections + $morning_classes[4]->nr_of_sections + $morning_classes[5]->nr_of_sections;
                            }
                            else{
                                for($i=0; $i<count($morning_classes)-1; $i++){
                                    if(isset($morning_classes[$i]->nr_of_students)){

                                    $morning_classes_string .= 'Τμ.'.($i+1).': '.$morning_classes[$i]->nr_of_students.' <strong>'.$morning_classes[$i]->nr_of_sections.'</strong> '.optional($morning_classes[$i])->comment.'<br>';
                                    $total_sections_number += $morning_classes[$i]->nr_of_sections;
                                }
                                }
                            }
                        }
                        else{
                            $morning_classes_string = 'Δεν έχει καταχωρηθεί';
                        }
                    @endphp
                    <td>{{ $total_sections_number }}</td>
                    <td>{!! $morning_classes_string !!}</td>
                    @php
                        $morning_zone_classes_json = $plan->morning_zone_classes;
                        if($morning_zone_classes_json){
                            $morning_zone_classes = json_decode($morning_zone_classes_json);
                            $morning_zone_classes_string = ' <strong> '.$morning_zone_classes[0]->nr_of_sections.'</strong>';
                        }
                    @endphp
                    <td>{!! $morning_zone_classes_string !!}</td>
                    @php
                    $all_day_school_classes_json = $plan->all_day_school_classes;
                    if($all_day_school_classes_json){
                        if($plan->enrollment->school->primary){
                            $all_day_school_classes = json_decode($all_day_school_classes_json);
                            $all_day_school_classes_string_z1 = $all_day_school_classes[0]->nr_of_students.' <strong> '.$all_day_school_classes[0]->nr_of_sections.'</strong>';
                        }
                        else{
                            $all_day_school_classes_string_z1='-';
                        }
                    }
                    else{
                        $all_day_school_classes_string_z1='Δεν έχει καταχωρηθεί';
                    }
                    @endphp
                    <td>{!!$all_day_school_classes_string_z1 !!}</td>
                    @php
                        if($all_day_school_classes_json){
                            if($plan->enrollment->school->primary){
                                $all_day_school_classes = json_decode($all_day_school_classes_json);
                                $all_day_school_classes_string_z2 = $all_day_school_classes[1]->nr_of_students.' <strong> '.$all_day_school_classes[1]->nr_of_sections.'</strong>';
                            }
                            else{
                               $all_day_school_classes = json_decode($all_day_school_classes_json);
                                $all_day_school_classes_string_z2 = $all_day_school_classes[0]->nr_of_students.' <strong> '.$all_day_school_classes[0]->nr_of_sections.'</strong>';
                            }
                        }
                        else{
                            $all_day_school_classes_string_z2='Δεν έχει καταχωρηθεί';
                        }
                    @endphp
                    <td>{!!$all_day_school_classes_string_z2 !!}</td>
                    @php
                        if($all_day_school_classes_json){
                            if($plan->enrollment->school->primary){
                                $all_day_school_classes = json_decode($all_day_school_classes_json);
                                $all_day_school_classes_string_z3 = $all_day_school_classes[2]->nr_of_students.' <strong> '.$all_day_school_classes[2]->nr_of_sections.'</strong>';
                            }
                            else{
                               $all_day_school_classes = json_decode($all_day_school_classes_json);
                                $all_day_school_classes_string_z3 = $all_day_school_classes[1]->nr_of_students.' <strong> '.$all_day_school_classes[1]->nr_of_sections.'</strong>';
                            }
                        }
                        else{
                            $all_day_school_classes_string_z3='Δεν έχει καταχωρηθεί';
                        }
                    @endphp
                    <td>{!!$all_day_school_classes_string_z3 !!}</td>
                    <td>{{ $plan->enrollment->school->code }}</td>
                </tr>   
            @endforeach
        </tbody>
    </table>
    </div>
</x-layout>