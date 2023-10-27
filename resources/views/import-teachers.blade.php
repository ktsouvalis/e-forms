<x-layout>
    <div class="container">
        @if(!session()->has('asks_to'))
        <nav class="navbar navbar-light bg-light">
            <form action="{{url('/upload_teachers_template')}}" method="post" class="container-fluid" enctype="multipart/form-data">
                @csrf
                <div class="vstack gap-3">
                    <div class="hstack gap-1">
                        <input  type="file" id="organiki" name="organiki_file" required>
                        <label class="px-1" for="organiki">Οργανικά Ανήκοντες (4.1 MYSCHOOL)</label><br>
                    </div>
                    <div class="hstack gap-1">
                        <input type="file" id="apospasi" name="apospasi_file" required>
                        <label class="px-1" for="apospasi">Αποσπασμένοι (4.2 MYSCHOOL)</label><br> 
                    </div>
                </div>
                <button type="submit" class="btn bi bi-filetype-xlsx btn-primary"> Αποστολή αρχείων</button>
            </form>
        </nav>
        <hr>
        <nav class="navbar navbar-light bg-light">
            <form action="{{url('/upload_teachers_template')}}" method="post" class="container-fluid" enctype="multipart/form-data">
                <div class="hstack gap-1">
                    <input type="radio" id="didaskalia" name="template_file" value="didaskalia">
                    <label class="px-1" for="didaskalia">Διδάσκουν (4.9 MYSCHOOL)</label><br> 
                </div> 
                <div class="hstack gap-1">
                    <input type="radio" id="apousia" name="template_file" value="apousia">
                    <label class="px-1" for="apousia">Απουσιάζουν </label><br> 
                    <div>4.16 MYSCHOOL: Πρέπει από το report να διαγραφούν οι αιτιολογήσεις απουσίας: ΑΠΕΥΘΕΙΑΣ ΑΠΟΣΠΑΣΗ ΣΕ ΣΧΟΛΙΚΗ ΜΟΝΑΔΑ, ΑΠΟΣΠΑΣΗ ΣΕ ΣΧΟΛΙΚΗ ΜΟΝΑΔΑ ΕΝΤΟΣ ΠΥΣΠΕ/ΠΥΣΔΕ, ΕΠΙ ΘΗΤΕΙΑ ΣΕ ΣΧΟΛΙΚΗ ΜΟΝΑΔΑ, ΟΛΙΚΗ ΔΙΑΘΕΣΗ ΣΕ ΣΧ. ΜΟΝΑΔΑ.<br> Πρέπει να σβηστούν τα ιδιωτικά σχολεία.</div>
                </div> 
                <input type="file" name="import_teachers" >     
                <button type="submit" class="btn bi bi-filetype-xlsx btn-primary"> Αποστολή αρχείου</button>
                </div>
            </form>
        </nav>
        @else
        
        <div style="p-3 mb-2 bg-info text-dark">
            Διαβάστηκαν:
        </div>
        <div class="table-responsive">
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
                        $sxesi = $teacher['sxesi_ergasias']!="Error: Κενό πεδίο" ? App\Models\SxesiErgasias::find($teacher['sxesi_ergasias'])->name : $teacher['sxesi_ergasias'] ;
                    @endphp
                    <td @if($sxesi=="Κενό πεδίο") style='color: red' @endif>{{$sxesi}}</td>

                    @isset($teacher['organiki_type'])
                        @php
                            $organiki = $teacher['organiki_type']::find($teacher['organiki']);   
                        @endphp
                        <td >{{$organiki->name}}</td>
                    @else
                        <td @if($teacher['organiki']=="Error: Άγνωστος κωδικός οργανικής") style='color: red' @endif> {{$teacher['organiki']}} </td>
                    @endisset
                    
                    {{-- <td>{{$teacher['ypiretisi']}}</td> --}}
                    <td>{{$teacher['org_eae']}}</td>
                </tr>
                {{-- @endif --}}
            @endforeach
        </table>
        </div>
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