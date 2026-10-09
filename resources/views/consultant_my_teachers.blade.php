<x-layout_consultant>

```
@push('title')
    <title>Εκπαιδευτικοί</title>
@endpush

@push('links')
    <link href="{{ asset('DataTables-1.13.4/css/dataTables.bootstrap5.css') }}" rel="stylesheet"/>
    <link href="{{ asset('Responsive-2.4.1/css/responsive.bootstrap5.css') }}" rel="stylesheet"/>
@endpush

@push('scripts')
    <script src="{{ asset('DataTables-1.13.4/js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('DataTables-1.13.4/js/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('Responsive-2.4.1/js/dataTables.responsive.js') }}"></script>
    <script src="{{ asset('Responsive-2.4.1/js/responsive.bootstrap5.js') }}"></script>
    <script src="{{ asset('/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            $('#dataTable').DataTable({
                responsive: true,
                pageLength: 25,
                language: {
                    search: "Αναζήτηση:",
                    lengthMenu: "Εμφάνιση _MENU_ εγγραφών",
                    info: "Εμφανίζονται _START_ έως _END_ από _TOTAL_ εγγραφές",
                    infoEmpty: "Δεν υπάρχουν εγγραφές",
                    zeroRecords: "Δεν βρέθηκαν εγγραφές",
                    paginate: {
                        first: "Πρώτη",
                        last: "Τελευταία",
                        next: "Επόμενη",
                        previous: "Προηγούμενη"
                    }
                }
            });
        });
    </script>
@endpush

<div class="container-fluid">

    <div class="row mb-3">

        <div class="col">
            <p class="h4">Εκπαιδευτικοί</p>

            <p class="text-muted">
                Οι εκπαιδευτικοί που υπηρετούν στα σχολεία της περιοχής ευθύνης σας.
            </p>

            <p class="text-muted">
                Κάνετε κλικ στο επώνυμο του εκπαιδευτικού για περισσότερες πληροφορίες.
            </p>
        </div>

    </div>

    <div class="table-responsive">

        <table id="dataTable"
               class="align-middle table table-sm table-striped table-bordered table-hover"
               style="font-size: small; width:100%">

            <thead>
                <tr>
                    <th>ΑΦΜ</th>
                    <th>Επώνυμο</th>
                    <th>Όνομα</th>
                    <th>Κλάδος</th>
                    <th>Σχ. Εργ.</th>
                    <th>Email</th>
                    <th>Υπηρέτηση</th>
                    <th>Δήμος Υπηρέτησης</th>
                    <th>Οργ.</th>
                </tr>
            </thead>

            <tbody>

                @foreach($teachers as $teacher)

                    <tr>

                        <td>
                            {{ $teacher->afm }}
                        </td>

                        <td>
                            {{ $teacher->surname }}
                        </td>

                        <td>
                            {{ $teacher->name }}
                        </td>

                        <td>
                            {{ $teacher->klados }}
                        </td>

                        <td>
                            {{ optional($teacher->sxesi_ergasias)->name }}
                        </td>

                        <td>
                            {{ $teacher->mail }}
                        </td>

                        <td>
                            {{ optional($teacher->ypiretisi)->name }}
                        </td>

                        <td>
                            {{ optional(optional($teacher->ypiretisi)->municipality)->name }}
                        </td>

                        <td>
                            {{ optional($teacher->organiki)->name }}
                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>
```

</x-layout_consultant>
