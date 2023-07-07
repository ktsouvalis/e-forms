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
        $directors_array = session('directors_array');
    @endphp
    <div class="container">
        <div class="p-3 mb-2 bg-success text-white">Διευθυντές που θα εισαχθούν</div>
        <div class="table-responsive">
            <table  id="" class="table table-sm table-success table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th id="search">Κωδικός Σχολείου</th>
                        <th id="search">Επωνυμία Σχολείου</th>
                        <th id="search">ΑΦΜ Διευθυντή</th>
                        <th id="search">Επώνυμο Διευθυντή</th>
                        
                    </tr>
                </thead>
                <tbody>
                @foreach($directors_array as $director)
                    <tr>
                        <td>{{$director['code']}}</td>
                        <td>{{$director['school_name']}}</td>
                        <td>{{$director['afm']}}</td>
                        <td>{{$director['director_surname']}}</td>
                        
                    </tr>
                    
                @endforeach
                </tbody>
            </table>
        </div><p></p>
        
        <div class="hstack gap-2">
        <form action="{{url("/insert_directors")}}" method="post" enctype="multipart/form-data>">
             @csrf
            <button type="submit" class="btn btn-primary bi bi-file-arrow-up p-2"> Αποστολή</button>
        </form>
        <a href="{{url('/directors')}}" class="col"> Ακύρωση</a>
        </div>
    </div>
</x-layout>