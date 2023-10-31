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
        <script src="copylink.js"></script>
        <script src="datatable_init.js"></script>
    @endpush
    @push('title')
        <title>Σύμβουλοι Εκπαίδευσης</title>
    @endpush
    @php
        $all_consultants = App\Models\Consultant::all();
    @endphp
<body>    
    <p class="h4">Σύμβουλοι Διεύθυνσης</p>
    <form action="{{url("share_links_to_all/consultant")}}" method="post">
        @csrf
        <button type="submit" class="mb-2 btn btn-warning bi bi-envelope-at" onclick="return confirm('Επιβεβαίωση μαζικής αποστολής;')"> Μαζική Αποστολή Συνδέσμων</button>
    </form>
    <div class="table-responsive">
        <table  id="dataTable" class="align-middle table table-sm table-striped table-bordered table-hover"  style="font-size: small">
            <thead>
                <tr>
                    <th class="align-middle">Αντιγραφή συνδέσμου</th>
                    <th class="align-middle">Αποστολή συνδέσμου</th>
                    <th id="search">ΑΦΜ</th>
                    <th id="search">AΜ</th>
                    <th id="search">email</th>
                    <th id="search">Επώνυμο</th>
                    <th id="search">Όνομα</th>
                    <th id="search">Κλάδος</th>     
                </tr>
            </thead>
            <tbody>
        
            @foreach($all_consultants as $consultant)
                @php
                    $text = url("/consultant/$consultant->md5");  
                @endphp
                
                <tr>  
                    <td style="text-align:center">
                        <button class="copy-button btn btn-outline-secondary bi bi-clipboard" data-clipboard-text="{{$text}}"> </button>
                    </td>
                    <td style="text-align:center" >
                        <form action="{{url("share_link/consultant/$consultant->id")}}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-warning bi bi-envelope-at"> </button>
                        </form>
                    </td> 
                    <td>{{$consultant->afm}}</td>
                    <td>{{$consultant->am}}</td>
                    <td>{{$consultant->mail}}</td>
                    <td>{{$consultant->surname}}</td>
                    <td>{{$consultant->name}}</td>
                    <td>{{$consultant->klados}}</td>
                </tr>
            @endforeach
        </tbody>
        </table>
    </div>
</x-layout>