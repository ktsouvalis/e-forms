<x-layout>
    @push('links')
        <link href="{{asset('DataTables-1.13.4/css/dataTables.bootstrap5.css')}}" rel="stylesheet"/>
        <link href="{{asset('Responsive-2.4.1/css/responsive.bootstrap5.css')}}" rel="stylesheet"/>
    @endpush

    @push('scripts')
        <script src="{{asset('DataTables-1.13.4/js/jquery.dataTables.js')}}"></script>
        <script src="{{asset('DataTables-1.13.4/js/dataTables.bootstrap5.js')}}"></script>
        <script src="{{asset('Responsive-2.4.1/js/dataTables.responsive.js')}}"></script>
        <script src="{{asset('Responsive-2.4.1/js/responsive.bootstrap5.js')}}"></script>
        <script src="{{asset('datatable_init.js')}}"></script>
    @endpush
    @push('title')
        <title>Εικονικός Μήνας</title>
    @endpush
    @php
        $schools = App\Models\School::all();
    @endphp
        <div class="table-responsive">
            <table  id="dataTable" class="align-middle table table-sm table-striped table-bordered table-hover" style="font-size: small;" >
                <thead>
                    <tr>
                        <th id="search">Ονομασία</th>
                        <th id="search">Κωδικός</th>
                        <th id="search">Διευθυντής</th>
                        <th id="search">Ενεργός Μήνας</th>
                        <th id="search">Εικονικός ενεργός Μήνας</th>
                        <th >Επαναφορά ενεργού Μήνα</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($schools as $school)
                    <tr>
                        <td >{{$school->name}}</td>
                        <td >{{$school->code}}</td>
                        {{-- @if($school->director)
                        <td>{{$school->director->surname}} {{$school->director->name}}</td>
                        @else
                        <td> - </td>
                        @endif --}}
                        <td>{{optional($school->director)->surname}} {{optional($school->director)->name}}</td>
                        @if($school->vmonth and $school->vmonth->vmonth != 0)
                        <td style="color:red"><strong>{{App\Models\Month::where('number',$school->vmonth->vmonth)->first()->name}}</strong></td>
                        @else
                        <td>{{App\Models\Month::getActiveMonth()->name}}</td>
                        @endif
                        @php
                            $months = [];
                            $number = App\Models\Month::getActiveMonth()->number;
                            $i=$number-1;
                            if($number >=9){
                                for($i; $i>=9; $i--)
                                    array_push($months, $i);
                            }
                            else{
                                for($i; $i>=1; $i--)
                                    array_push($months, $i);
                                array_push($months,12,11,10,9);
                            }
                        @endphp
                        <td>
                            <form action="{{url("/set_vmonth/$school->id")}}" method="post">
                            @csrf
                            <input name="month" id="month" type="text" class="form-control" placeholder="Επιλέξτε Μήνα" aria-label="Μήνας" aria-describedby="basic-addon2" required list="monthOptions">
                            <datalist id="monthOptions">
                                @foreach($months as $month)
                                    @php
                                        $show = App\Models\Month::where('number', $month)->first()->name;
                                    @endphp
                                    <option value="{{ $show }}">{{ $show }}</option>
                                @endforeach
                            </datalist>
                            <button type="submit" class="btn btn-warning m-2"><div class="fa-regular fa-calendar-plus"></div> </button>
                            </form>
                        </td>
                        <td>
                            <form action="{{url("/reset_active_month/$school->id")}}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-primary m-2"><div class="fa-regular fa-calendar-check"></div> </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            </table>
            </div>
</x-layout>