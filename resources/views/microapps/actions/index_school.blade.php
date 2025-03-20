<x-layout>
    @push('links')
        <link href="{{asset('DataTables-1.13.4/css/dataTables.bootstrap5.css')}}" rel="stylesheet"/>
        <link href="{{asset('Responsive-2.4.1/css/responsive.bootstrap5.css')}}" rel="stylesheet"/>
    @endpush

    @push('scripts')
        <script src="{{asset('DataTables-1.13.4/js/jquery.dataTables.js')}}"></script>
        <script src="{{asset('DataTables-1.13.4/js/dataTables.bootstrap5.js')}}"></script>
        <script src="{{asset('Responsive-2.4.1/js/dataTables.responsive.js')}}"></script>
        <script src="{{asset('Responsive-2.4.1/js/responsive.bootstrap5.js')}}"></script>
        <script src="{{asset('datatable_init.js')}}"></script>
        <script src="{{asset('toggle_signed_internal_rules.js')}}"></script>
        <script src="{{asset('datatable_init_internal_rules_second.js')}}"></script>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const checkboxes = document.querySelectorAll(".action-checkbox");

                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener("change", function () {
                        checkboxes.forEach(cb => {
                            if (cb !== this) {
                                cb.checked = false;
                            }
                        });
                    });
                });

                document.getElementById("confirmSelection").addEventListener("click", function () {
                    const selectedAction = document.querySelector(".action-checkbox:checked");
                    if (selectedAction) {
                         sendActionBack()
                        //alert("You selected action ID: " + selectedAction.value);
                    } else {
                        alert("Δεν έχετε επιλέξει κάποια δράση!");
                    }
                });
            });
        </script>
    @endpush

    @push('title')
        <title>Εκπαιδευτικές Δράσεις</title>
    @endpush

    @php
        $user = Auth::guard('school')->user();
        $actions = App\Models\microapps\Action::where('school_id', $user->id)->get();
        $action_types = App\Models\microapps\Actiontype::get();
    @endphp

    <div class="container pt-2">
        <div class="h4">Εκπαιδευτικές Δράσεις</div>
    </div>

    <table id="dataTable" class="small display align-middle table table-sm table-secondary table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>Επιλογή</th>
                <th id="search">Τίτλος Δράσης</th>
                <th id="search">Εκπαιδευτικοί</th>
                <th>Αρχεία</th>
                <th>Ενέργειες</th>
                <th>Κατηγορία</th>
            </tr>
        </thead>
        <tbody>
            @if($actions->isEmpty())
                <tr>
                    <td colspan="6">Δεν υπάρχουν εκπαιδευτικές δράσεις</td>
                </tr>
            @else
                @foreach($actions as $action)
                    <tr>
                        <td>
                            <input type="checkbox" class="action-checkbox" value="{{$action->id}}">
                        </td>
                        <td class="action-title" data-id="{{ $action->id }}">{{ $action->title }}</td>
                        <td>{{$action->teachers}}</td>
                        <td>{{$action->records}}</td>
                        <td>
                            <a href="{{route('actions.edit', $action->id)}}" class="btn btn-primary">Επεξεργασία</a>
                            <form action="{{route('actions.destroy', $action->id)}}" method="POST" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Διαγραφή</button>
                            </form>
                        </td>
                        <td>{{$action->type->description}}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <div class="mt-3">
        <button id="confirmSelection" class="btn btn-info">Προσθήκη της επιλεγμένης Δράσης</button>
        <a href="{{route('actions.create')}}" class="btn btn-success">Δημιουργία Νέας Εκπ. Δράσης</a>
    </div>

    <script>
        
        function sendActionBack() {
                
        const checkedCheckbox = document.querySelector('.action-checkbox:checked');
        
        if (checkedCheckbox) {
            // Get the action ID from the checkbox value
            const actionId = checkedCheckbox.value;
            
            // Find the corresponding title cell in the same row
            const titleCell = checkedCheckbox.closest('tr').querySelector('.action-title');
            
            const actionData = {
                id: actionId,
                title: titleCell.textContent.trim(),
                // You could add more fields here by selecting other cells in the same row
            };
            
            // Send the action data back to the main window
            window.opener.postMessage({ type: 'ACTION_SELECTED', data: actionData }, '*');
            // Close the window after sending the data
            window.close();
        }
    }
    </script>
</x-layout>
