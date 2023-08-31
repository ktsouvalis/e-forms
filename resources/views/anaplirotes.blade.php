
    <x-layout_teacher>

        @push('links')
        <link href="DataTables-1.13.4/css/dataTables.bootstrap5.css" rel="stylesheet"/>
        <link href="Responsive-2.4.1/css/responsive.bootstrap5.css" rel="stylesheet"/>
    @endpush

    @push('scripts')
        <script src="DataTables-1.13.4/js/jquery.dataTables.js"></script>
        <script src="DataTables-1.13.4/js/dataTables.bootstrap5.js"></script>
        <script src="Responsive-2.4.1/js/dataTables.responsive.js"></script>
        <script src="Responsive-2.4.1/js/responsive.bootstrap5.js"></script>
        <script src="datatable_init.js"></script>
    @endpush

    @push('title')
        <title>Χρήστες</title>
    @endpush


   <div class="container">
    <div class="table-responsive">
        <table  id="dataTable" class="align-middle display table  table-striped table-hover" style="font-size:small">
        <thead>
            <tr>
                <th id="search">Επώνυμο</th>
                <th id="search">Όνομα</th>
                <th id="search">Πατρώνυμο</th>
                <th id="search">Κλάδος</th>
                <th id="search">Έργο</th>
                
            </tr>
        </thead>
        <tbody>
           
                <tr>  
                    <td>Στεφανόπουλος</td>
                    <td>Κωνσταντίνος</td>
                    <td>Νικόλαος</td>
                    <td>ΠΕ11</td>
                    <td>Έργο 1</td>
                </tr>
                <tr>  
                    <td>Τσούβαλης</td>
                    <td>Προκόπης</td>
                    <td>Τζόναθαν</td>
                    <td>ΠΕ60</td>
                    <td>Έργο 2</td>
                </tr>
            
        </tbody>
        </table>
    </div>

</x-layout_teacher>
  </html>