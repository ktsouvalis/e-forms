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
                var codeColumn = document.querySelectorAll("#dataTable tbody td:nth-child(4)");
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
                alert("Αντιγράφτηκαν " + codeValues.length + " ΑΦΜ για επικόλληση σε αρχείο xlsx!");
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
        <title>Εκπαιδευτικοί</title>
    @endpush
    @php
        $all_teachers = App\Models\Teacher::all();
    @endphp
<body>

    <button class="btn btn-secondary bi bi-clipboard" id="copyCodeButton"> Αντιγραφή ΑΦΜ εκπαιδευτικών</button>
    <div class="table-responsive">
        <table  id="dataTable" class="align-middle table table-sm table-striped table-bordered table-hover"  style="font-size: small">
            <thead>
                <tr>
                    
                    {{-- <th id="search">id</th> --}}
                    <th class="align-middle">Αντιγραφή συνδέσμου</th>
                    <th class="align-middle">Αποστολή συνδέσμου</th>
                    <th id="search">AΜ</th>
                    <th id="search">ΑΦΜ</th>
                    <th id="search">Επώνυμο</th>
                    <th id="search">Όνομα</th>
                    <th id="search">Κλάδος</th>
                    <th id="search">Σχέση Εργασίας</th>
                    
                    <th id="search">email</th>
                    <th id="search">email ΠΣΔ</th>
                    <th id="search">Τηλέφωνο</th>
                    
                    <th id="search">Οργανική</th>
                    {{-- <th id="search">Υπηρέτηση</th> --}}
                    <th id="search">last login</th>
                    
                    
                </tr>
            </thead>
            <tbody>
            @foreach($all_teachers as $teacher)
                @php
                    $date=null;
                    if($teacher->logged_in_at) 
                        $date = Illuminate\Support\Carbon::parse($teacher->logged_in_at);
                    $text = url("/teacher/$teacher->md5");
                @endphp
                <tr>  
                    
                    {{-- <td>{{$teacher->id}}</td> --}}
                    <td style="text-align:center">
                        <button class="copy-button btn btn-secondary bi bi-clipboard" data-clipboard-text="{{$text}}"> </button>
                    </td>
                    <td style="text-align:center" >
                        <form action="{{url("share_link/teacher/$teacher->id")}}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-warning bi bi-envelope-at"> </button>
                        </form>
                    </td> 
                    <td>{{$teacher->am}}</td>
                    <td>{{$teacher->afm}}</td>
                    <td>{{$teacher->surname}}</td>
                    <td>{{$teacher->name}}</td>
                    <td>{{$teacher->klados}}</td>
                    <td>{{$teacher->sxesi_ergasias->name}}</td>
                     
                    <td>{{$teacher->mail}}</td>
                    <td>{{$teacher->sch_mail}}</td>
                    <td>{{$teacher->telephone}}</td>
                    
                    <td>{{$teacher->organiki->name}}</td>
                    {{-- <td>{{$teacher->ypiretisi->name}}</td> --}}
                    @if($date)
                        <td>{{$date->day}}/{{$date->month}}/{{$date->year}}</td>
                    @else
                        <td> - </td>
                    @endif 
                </tr>
            @endforeach
        </tbody>
        </table>
    </div>
        
    @can('upload', App\Models\Teacher::class)
        <a href="{{url('/import_teachers')}}" class="btn btn-primary bi bi-person-lines-fill"> Μαζική Εισαγωγή Εκπαιδευτικών</a>
    @endcan

</x-layout>