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
        <script>
            document.getElementById("copyCodeButton").addEventListener("click", function() {
                var codeColumn = document.querySelectorAll("#dataTable tbody td:nth-child(3)");
                var codeValues = Array.from(codeColumn).map(function(cell) {
                    return cell.textContent.trim();
                });

                // Create a temporary text area element
                var tempTextArea = document.createElement("textarea");

                // Set the code values as the text area's value, separated by newlines
                tempTextArea.value = codeValues.join("\n");

                // Append the text area to the document body
                document.body.appendChild(tempTextArea);

                // Programmatically select the text within the text area
                tempTextArea.select();

                // Execute the copy command to copy the selected text to the clipboard
                document.execCommand("copy");

                // Remove the temporary text area from the document
                document.body.removeChild(tempTextArea);

                // Optionally, provide user feedback (e.g., show a success message)
                alert("Αντιγράφτηκαν " + codeValues.length + " κωδικοί για επικόλληση σε αρχείο xlsx!");
                });
        </script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var clipboard = new ClipboardJS('.copy-button');

                clipboard.on('success', function(e) {
                    alert('Αντιγράφτηκε: ' + e.text);
                });

                clipboard.on('error', function(e) {
                    alert('Αποτυχία αντιγραφής');
                });
            });
        </script>
    @endpush
    @push('title')
        <title>Σχολεία</title>
    @endpush
    @php
        $all_schools = App\Models\School::all();
    @endphp
<body>
{{-- <div class="container"> --}}
    <button class="btn btn-secondary bi bi-clipboard" id="copyCodeButton"> Αντιγραφή κωδικών σχολείων</button>
    <div class="table-responsive">
        <table  id="dataTable" class="align-middle table table-sm table-striped table-bordered table-hover" style="font-size: small;" >
            <thead>
                <tr>
                    {{-- <th id="search">id</th> --}}
                    <th class="align-middle">Αντιγραφή συνδέσμου</th>
                    <th class="align-middle">Αποστολή συνδέσμου</th>
                    <th id="search">Κωδικός</th>
                    <th id="search">Ονομασία</th>
                    <th id="search">email</th>
                    <th id="search">tel</th>
                    <th id="search">Δήμος</th>
                    <th id="search">last login</th>
                    
                </tr>
            </thead>
            <tbody>
            @foreach($all_schools as $school)
                @php
                    $date=null;
                    if($school->logged_in_at) 
                        $date = Illuminate\Support\Carbon::parse($school->logged_in_at);
                    $text = url("/school/$school->md5");
                @endphp
                <tr>
                    {{-- <td >{{$school->id}}</td> --}}
                    <td style="text-align:center">
                        <button class="copy-button btn btn-secondary bi bi-clipboard" data-clipboard-text="{{$text}}"> </button>
                    </td>
                    <td style="text-align:center;">
                        <form action="{{url("share_link/school/$school->id")}}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-warning bi bi-envelope-at"> </button>
                        </form>
                    </td>  
                    <td >{{$school->code}}</td>
                    <td >{{$school->name}}</td>
                    <td >{{$school->mail}}</td>
                    <td >{{$school->telephone}}</td>
                    <td >{{$school->municipality->name}}</td>
                    @if($date)
                        <td >{{$date->day}}/{{$date->month}}/{{$date->year}}</td>
                    @else
                        <td > - </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
        </table>
    </div>
    
    @can('upload', App\Models\School::class)
        <a href="{{url('/import_schools')}}" class="btn btn-primary bi bi-building-up"> Μαζική Εισαγωγή Σχολείων</a>
    @endcan
{{-- </div> --}}
</x-layout>
        
           