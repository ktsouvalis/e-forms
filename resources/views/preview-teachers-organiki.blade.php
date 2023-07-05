<x-layout>
    @push('links')
        <link href="DataTables-1.13.4/css/dataTables.bootstrap5.css" rel="stylesheet"/>
        <link href="Responsive-2.4.1/css/responsive.bootstrap5.css" rel="stylesheet"/>
    @endpush

    @push('scripts')
        <script src="DataTables-1.13.4/js/jquery.dataTables.js"></script>
        <script src="DataTables-1.13.4/js/dataTables.bootstrap5.js"></script>
        <script src="Responsive-2.4.1/js/dataTables.responsive.js"></script>
        <script src="Responsive-2.4.1/js/responsive.bootstrap5.js"></script>
    @endpush
    @push('title')
        <title>Προεπισκόπηση</title>
    @endpush
    @php
        $teachers_array = session('teachers_array');
    @endphp
    <div class="container">
        <div class="p-3 mb-2 bg-success text-white">Εκπαιδευτικοί που θα εισαχθούν ή θα ανανεωθούν</div>
        <div class="table-responsive">
            <table  id="" class="table table-sm table-success table-striped table-bordered table-hover">
                <thead>
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
                </thead>
                <tbody>
                @foreach($teachers_array as $teacher)
                    
                    {{-- <tr class="bg-success" style="opacity:0.7;">  --}}
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
                        <td>{{$teacher['sxesi_ergasias_name']}}</td>
                        <td>{{$teacher['organiki_name']}}</td>
                        {{-- <td>{{$teacher->ypiretisi->name}}</td> --}}
                        @if($teacher['org_eae'])
                            <td>ΝΑΙ</td>
                        @else
                            <td> - </td>
                        @endif
                    </tr>
                    
                @endforeach
                </tbody>
            </table>
        </div><p></p>
        
        <div> Για τους εκπαιδευτικούς που βρίσκονται ήδη στη βάση θα ανανεωθούν τα στοιχεία τους (εκτός του ΑΦΜ) σύμφωνα με τις αλλαγές που περιέχονται στο αρχείο από το myschool που ανεβάσατε</div><br>
        <div class="hstack gap-2">
        <form action="{{url("/insert_teachers_organiki")}}" method="post" enctype="multipart/form-data>">
             @csrf
            <button type="submit" class="btn btn-primary bi bi-file-arrow-up p-2"> Αποστολή</button>
        </form>
        <a href="{{url('/teachers')}}" class="col"> Ακύρωση</a>
        </div>
    </div>
</x-layout>