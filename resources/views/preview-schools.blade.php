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
        $schools_array = session('schools_array');
        $codes = array_column($schools_array, 'code');
        $recordsToDelete = App\Models\School::whereNotIn('code', $codes)->get();
    @endphp
    <div class="container">
        Σχολεία που θα εισαχθούν εκ νέου
        <div class="table-responsive">
            <table  id="" class="table table-sm table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th id="search">Κωδικός</th>
                        <th id="search">Ονομασία</th>
                        <th id="search">email</th>
                        <th id="search">link</th>
                        <th id="search">Δημ/Νηπ</th>
                        <th id="search">Ειδικό</th>
                        <th id="search">Δημόσιο</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($schools_array as $school)
                    @if($school['action']=='')
                    {{-- <tr class="bg-success" style="opacity:0.7;">  --}}
                    <tr>
                        <td>{{$school['code']}}</td>
                        <td>{{$school['name']}}</td>
                        <td>{{$school['mail']}}</td>
                        <td>{{$school['md5']}}</td>
                        <td>{{$school['primary']}}</td>
                        <td>{{$school['special_needs']}}</td>
                        <td>{{$school['international']}}</td>
                    </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div><p></p>
        Σχολεία που θα διαγραφούν
        <div class="table-responsive">
            <table  id="" class=" table table-sm table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th id="search">Κωδικός</th>
                        <th id="search">Ονομασία</th>
                        <th id="search">email</th>
                        <th id="search">link</th>
                        <th id="search">Δημ/Νηπ</th>
                        <th id="search">Ειδικό</th>
                        <th id="search">Δημόσιο</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($recordsToDelete as $school)
                    {{-- <tr class="bg-warning" style="opacity:0.7;">  --}}
                    <tr>
                        <td>{{$school->code}}</td>
                        <td>{{$school->name}}</td>
                        <td>{{$school->mail}}</td>
                        <td>{{$school->md5}}</td>
                        <td>{{$school->primary}}</td>
                        <td>{{$school->special_needs}}</td>
                        <td>{{$school->international}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div> Για τα Σχολεία που βρίσκονται ήδη στη βάση θα ανανεωθούν τα στοιχεία τους σύμφωνα με τις αλλαγές που περιέχονται στο αρχείο από το myschool που ανεβάσατε</div><br>
        <div class="hstack gap-2">
        <form action="{{url("/insert_schools")}}" method="post" enctype="multipart/form-data>">
             @csrf
            <button type="submit" class="btn btn-primary bi bi-file-arrow-up p-2"> Αποστολή</button>
        </form>
        <a href="{{url('/schools')}}" class="col"> Ακύρωση</a>
        </div>
    </div>
</x-layout>