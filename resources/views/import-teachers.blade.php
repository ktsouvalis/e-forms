<x-layout>
    <div class="container">
        @if(!session()->has('asks_to'))
        <nav class="navbar navbar-light bg-light">
            <form action="{{url('/upload_teachers_organiki_template')}}" method="post" class="container-fluid" enctype="multipart/form-data">
                @csrf
                <input type="file" name="import_teachers_organiki" >    
                <button type="submit" class="btn bi bi-filetype-xlsx btn-primary"> Αποστολή αρχείου</button>
            </form>
        </nav>
        @else
        
        <div style="p-3 mb-2 bg-info text-dark">
            Διαβάστηκαν:
        </div>
        
        <table class="table table-striped table-hover table-light">
            <tr>
                <th id="search">AΜ</th>
                <th id="search">ΑΦΜ</th>
                <th id="search">Επώνυμο</th>
                <th id="search">Όνομα</th>
                <th id="search">Πατρώνυμο</th>
                <th id="search">Μητρώνυμο</th>
                <th id="search">Κλάδος</th>
                <th id="search">email</th>
                <th id="search">email ΠΣΔ</th>
                <th id="search">Τηλέφωνο</th>
                <th id="search">Σχέση Εργασίας</th>
                <th id="search">Οργανική</th>
                {{-- <th id="search">Υπηρέτηση</th> --}}
                <th id="search">Οργανική στην Ειδική Αγωγή</th>
            </tr>
            
            @foreach(session('teachers_array') as $teacher)
                {{-- @if(!null==session("errors_array[$row]")) --}}
                <tr>  
                    <td>{{$teacher['am']}}</td>
                    <td>{{$teacher['afm']}}</td>
                    <td>{{$teacher['surname']}}</td>
                    <td>{{$teacher['name']}}</td>
                    <td>{{$teacher['fname']}}</td>
                    <td>{{$teacher['mname']}}</td>
                    <td>{{$teacher['klados']}}</td>
                    <td>{{$teacher['mail']}}</td>
                    <td>{{$teacher['sch_mail']}}</td>
                    <td>{{$teacher['telephone']}}</td>

                    @php
                        $sxesi = $teacher['sxesi_ergasias']!="Κενό πεδίο" ? App\Models\SxesiErgasias::find($teacher['sxesi_ergasias'])->name : $teacher['sxesi_ergasias'] ;
                    @endphp
                    <td @if($sxesi=="Κενό πεδίο") style='color: red' @endif>{{$sxesi}}</td>

                    @isset($teacher['organiki_type'])
                        @php
                            $organiki = $teacher['organiki_type']::find($teacher['organiki']);   
                        @endphp
                        <td >{{$organiki->name}}</td>
                    @else
                        <td @if($teacher['organiki']=="Άγνωστος κωδικός οργανικής") style='color: red' @endif> {{$teacher['organiki']}} </td>
                    @endisset
                    
                    {{-- <td>{{$teacher['ypiretisi']}}</td> --}}
                    <td>{{$teacher['org_eae']}}</td>
                </tr>
                {{-- @endif --}}
            @endforeach
        </table>
        
        @if(session('asks_to')=='save')
            <div class="row">
                <form action="{{url('/preview_teachers_organiki')}}" method="get" class="col container-fluid" enctype="multipart/form-data">
                @csrf
                    <button type="submit" class="btn btn-primary bi bi-search"> Προεπισκόπηση</button>
                </form>
                <a href="{{url('/teachers')}}" class="col">Ακύρωση</a>
            </div>
        @else
            <div class="row">
                <div>
                    Διορθώστε τα σημειωμένα σφάλματα και υποβάλετε εκ νέου το αρχείο.
                </div>
                <a href="{{url('/teachers')}}" class="col">Ακύρωση</a>
            </div>
        @endif
    @endif  
    </div>
</x-layout>