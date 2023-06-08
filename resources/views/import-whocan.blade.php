<x-layout>
    <div class="container">
        @empty($asks_to)
        <nav class="navbar navbar-light bg-light">
            <form action="{{url("/upload_whocan/$microapp->id")}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="upload_whocan" > 
                    <div class="hstack">
                            <div class="px-2"> Ανεβάζω:</div>
                            <div class="vstack px-2">
                                <div class="hstack">
                                    <input  type="radio" id="schools" name="template_file" value="schools" checked>
                                    <label class="px-1" for="schools">Σχολεία</label><br>
                                </div>
                                <div class="hstack">
                                    <input type="radio" id="teachers" name="template_file" value="teachers">
                                    <label class="px-1" for="teachers">Εκπαιδευτικούς</label><br> 
                                </div>
                            </div>
                        </div>   
                    <button type="submit" class="btn bi bi-filetype-xlsx btn-primary"> Αποστολή αρχείου</button>
            </form>
        </nav>
        @else
        
        <div style="p-3 mb-2 bg-info text-dark">
            Διαβάστηκαν:
        </div>
        <div class="table-responsive">
        @if($who=='schools')
        <table class="table table-striped table-hover table-light">
            <tr>
                <th id="search">Κωδικός</th>
                <th id="search">Ονομασία</th> 
            </tr>

            @foreach($whocan_array as $code)
                <tr>
                    @if($code=="Error: Άγνωστος κωδικός σχολείου")
                        <td style='color: red'>{{$code}}</td>
                        <td> - </td>
                    @else
                        @php    
                            $school=App\Models\School::where('code', $code)->first();
                        @endphp
                        <td>{{$school->code}}</td>
                        <td>{{$school->name}}</td>
                    @endif
                </tr>
            @endforeach
        </table>
        @else
        <table class="table table-striped table-hover table-light">
            <tr>
                <th id="search">ΑΦΜ</th>
                <th id="search">Επώνυμο</th> 
                <th id="search">Όνομα</th>
            </tr>

            @foreach($whocan_array as $afm)
                <tr>
                    @if($afm=="Error: Άγνωστος ΑΦΜ εκπαιδευτικού")
                        <td style='color: red'>{{$afm}}</td>
                        <td> - </td>
                        <td> - </td>
                    @else
                        @php    
                            $teacher=App\Models\Teacher::where('afm', $afm)->first();
                        @endphp
                        <td>{{$teacher->afm}}</td>
                        <td>{{$teacher->surname}}</td>
                        <td>{{$teacher->name}}</td>
                    @endif
                </tr>
            @endforeach
        </table>
        @endif
        </div>
        @if($asks_to=='save')
            <div class="hstack gap-3">
                <form action="{{url("/insert_whocan/$microapp->id")}}" method="post" enctype="multipart/form-data>">
                    @csrf
                    <button type="submit" class="btn btn-primary bi bi-file-arrow-up p-2 col"> Αποστολή</button>
                </form>
                <a href="{{url("/microapp_profile/$microapp->id")}}"> Ακύρωση</a>
            </div>
        @else
            <div class="hstack gap-3">
                <div>
                    Διορθώστε τα σημειωμένα σφάλματα και υποβάλετε εκ νέου το αρχείο.
                </div>
                <a href="{{url("/microapp_profile/$microapp->id")}}" >Ακύρωση</a>
            </div>
        @endif
    @endif  
    </div>
</x-layout>