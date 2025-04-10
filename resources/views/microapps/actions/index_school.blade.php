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
        
        function sendActionBack() {
            const selectedCheckboxes = document.querySelectorAll(".action-checkbox:checked");
            const checkboxId = @json(request('checkboxId'));

            if (selectedCheckboxes.length === 0) {
                alert("Δεν έχετε επιλέξει κάποια δράση!");
                return;
            }
            
            // Gather selected actions
            const selectedActions = Array.from(selectedCheckboxes).map(cb => {
                return {
                    actionId: cb.value,
                    title: cb.closest('tr').querySelector('.action-title').textContent.trim()
                };
            });

            // For new outing creation
            if(checkboxId == 'openActionCheckbox') {
                const actionData = {
                    outingId: "new",
                    actions: selectedActions,
                    oldOuting: 'False',
                }; 
                
                console.log("Action Data:", actionData);
                window.opener.postMessage({ type: 'ACTION_SELECTED', data: actionData }, '*');
                window.close();
            } 
            // For existing outing
            else { 
                const outingId = checkboxId;

                // Gather selected actions
                const selectedActions = Array.from(selectedCheckboxes).map(cb => {
                    return {
                        actionId: cb.value,
                        title: cb.closest('tr').querySelector('.action-title').textContent.trim()
                    };
                });
                //console.log("Selected Actions:", selectedActions.map(action => action.actionId));
                // AJAX request to update the outing with the selected action_ids
                fetch("{{ route('outings.update_outing_action') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        outing_id: outingId,
                        action_ids: selectedActions.map(action => action.actionId) // Send array of action IDs
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const actionData = {
                            outingId: outingId,
                            actions: selectedActions,
                            oldOuting: 'True',
                        };
                        window.opener.postMessage({ type: 'ACTION_SELECTED', data: actionData }, '*');
                        window.close();
                    } else {
                        alert("Σφάλμα κατά την ενημέρωση της δράσης.");
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("Πρόβλημα με την ενημέρωση της δράσης.");
                });
            }
        }
        </script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                
                const showConfirmButton = @json(request('checkboxId')) ? true : false;
                //alert("showConfirmButton: " + showConfirmButton); // Debugging line
                // Show or hide the button Προσθήκη της επιλεγμένης Δράσης based on the variable
                // When the user selects an action from the list, the button Add Action should be shown
                // When the user selects the button Action Handling, the button Διαχείριση Δράσης should be shown
                const confirmButton = document.getElementById("confirmSelection");
                const addActionBtn = document.getElementById("addActionBtn");
                if (showConfirmButton) {
                    confirmButton.style.removeProperty("display"); // Remove the display property entirely
                    addActionBtn.style.display = "none"; // Hide the "Δημιουργία Νέας Εκπ. Δράσης" button
                } else { 
                    confirmButton.style.display = "none";
                }
                
                // const checkboxes = document.querySelectorAll(".action-checkbox");

                // checkboxes.forEach(checkbox => {
                //     checkbox.addEventListener("change", function () {
                //         checkboxes.forEach(cb => {
                //             if (cb !== this) {
                //                 cb.checked = false;
                //             }
                //         });
                //     });
                // });

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
        $action_types = App\Models\microapps\ActionType::get();
        //dd($request->checkboxId);
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
                {{-- <tr>
                    <td colspan="6">Δεν υπάρχουν εκπαιδευτικές δράσεις</td>
                </tr> --}}
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
        <button id="confirmSelection" class="btn btn-info" style="display: none;">Προσθήκη της επιλεγμένης Δράσης</button>
        <a id="addActionBtn" href="{{route('actions.create')}}" class="btn btn-success">Δημιουργία Νέας Εκπ. Δράσης</a>
    </div>

</x-layout>
