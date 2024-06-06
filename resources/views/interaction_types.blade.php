<x-layout>
    @php
        $user = Illuminate\Support\Facades\Auth::guard('web')->user();
        $departments = App\Models\Department::where('name','LIKE','%Τμήμα%')->get();    
    @endphp
    @push('links')
        {{-- <link href="{{asset('DataTables-1.13.4/css/dataTables.bootstrap5.css')}}" rel="stylesheet"/>
        <link href="{{asset('Responsive-2.4.1/css/responsive.bootstrap5.css')}}" rel="stylesheet"/> --}}
    @endpush
    @push('scripts')
        {{-- <script src="{{asset('DataTables-1.13.4/js/jquery.dataTables.js')}}"></script>
        <script src="{{asset('DataTables-1.13.4/js/dataTables.bootstrap5.js')}}"></script>
        <script src="{{asset('Responsive-2.4.1/js/dataTables.responsive.js')}}"></script>
        <script src="{{asset('Responsive-2.4.1/js/responsive.bootstrap5.js')}}"></script> --}}
        {{-- <script src="{{asset('datatable_init.js')}}"></script> --}}
        <script>
            var checkTypeUrl = '{{ route("interaction_types.changeStatus", ["type" =>"mpla"]) }}';
            var updateTypeUrl = '{{ route("interaction_types.update", ["interaction_type" =>"mpla"]) }}';
        </script>    
        <script>
            $(document).ready(function() {
                $('.active-checkbox').change(function() {
                    var isActive = $(this).is(':checked') ? 1 : 0;
                    var typeId = $(this).data('id');

                    $.ajax({
                        url: checkTypeUrl.replace("mpla", typeId),
                        method: 'POST',
                        data: {
                            active: isActive,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            // Handle success here
                            console.log(response);
                            if (response.active==1) {
                                $('#row_' + typeId).addClass('table-success');
                            } else {
                                $('#row_' + typeId).removeClass('table-success');
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            // Handle error here
                            console.log(textStatus, errorThrown);
                        }
                    });
                });
            });
        </script>
        <script>
            $(document).ready(function() {
                $('.editable').focusout(function() {
                    var field = $(this).data('field');
                    var id = $(this).data('id');
                    var value = $(this).text();

                    $.ajax({
                        url: updateTypeUrl.replace("mpla", id),
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'PATCH',
                            field: field,
                            value: value,
                           
                        },
                        success: function(response) {
                            // Handle success here
                            console.log(response);
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            // Handle error here
                            console.log(textStatus, errorThrown);
                        }
                    });
                });
            });
        </script>
    @endpush
    <div class="container">
        <form method="POST" action="{{route('interaction_types.store')}}">
            @csrf
            <div class="form-group my-3">
                <label for="name"><strong>Όνομα</strong></label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>

            <div class="form-group my-3">
                <label for="folder"><strong>Φάκελος</strong></label>
                <input type="text" id="folder" name="folder" class="form-control" required>
            </div>
             <div class="form-group">
                <label for="stakes"><strong>Απευθύνεται σε</strong></label>
                <select id="stakes" name="stakes" class="form-control">
                    <option value="s">Σχολεία</option>
                    <option value="t">Εκπαιδευτικούς</option>
                </select>
            </div>
            @can('create_type_on_behalf', App\Models\InteractionType::class)
            <div class="form-group">
                <label for="department_id"><strong>Τμήμα</strong></label>
                <select id="department_id" name="department_id" class="form-control">
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
            @endcan
            <div class="input-group my-3">
                <button type="submit" class="btn btn-primary bi bi-plus-circle"> Προσθήκη</button>
            </div>
        </form>
    </div>
    <div class="container">
        <div class="table-responsive py-2">
            <table id="dataTable" class="display table table-sm table-striped table-hover">
            <thead>
                <tr>
                    @can('create_type_on_behalf', App\Models\InteractionType::class)
                        <th id="search">Τμήμα</th>
                    @endcan
                    <th id="search">Όνομα <i class="bi bi-pencil"></i></th>
                    <th id="search">Φάκελος <i class="bi bi-pencil"></i></th>
                    <th id="search">Απευθύνεται σε</th>
                    <th >Ενεργό</th>
                    @can('deleteAny', App\Models\InteractionType::class)
                    <th>Διαγραφή</th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                @foreach($interactionTypes as $type)
                    <tr id="row_{{$type->id}}" @if($type->active) class="table-success"@endif>
                        @can('create_type_on_behalf', App\Models\InteractionType::class)
                            <td>{{$type->department->name}}</td>
                        @endcan
                        <td class="editable" contenteditable="true" data-field="name" data-id="{{ $type->id }}">{{$type->name}}</td>
                        <td class="editable" contenteditable="true" data-field="folder" data-id="{{ $type->id }}">{{$type->folder}}</td>
                        <td>@if($type->stakes_to=='s')Σχολεία @else Εκπαιδευτικούς @endif</td>
                        <td>
                            <input type="checkbox" class="active-checkbox" data-id="{{ $type->id }}" {{ $type->active ? 'checked' : '' }}>  
                        </td>
                        @can('deleteAny', App\Models\InteractionType::class)
                        <td>
                            <form method="POST" action="{{route('interaction_types.destroy', ['interaction_type' => $type->id])}}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger bi bi-trash" onClick="return confirm('Να διαγραφεί;')"></button>
                            </form>
                        </td>
                        @endcan
                    </tr>
                @endforeach
            </tbody>
            </table>
        </div>
    </div>
</x-layout>
    