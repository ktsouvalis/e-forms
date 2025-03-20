<x-layout_consultant>
@php
    //use App\Models\Actiontype;
@endphp

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
    @endpush
    @push('title')
        <title>Κατηγορίες Εκπαιδευτικών Δράσεων</title>
    @endpush
    @php
        $user = Auth::guard('consultant')->user(); //check which user is logged in
        $action_types = App\Models\microapps\Actiontype::get();  //get the action types
        //dd($action_types);
    @endphp
    <div class="container pt-2">
        <div class="h4">Κατηγορίες Εκπαιδευτικών Δράσεων</div>
    </div>
    <table  id="dataTable" class="small display align-middle table table-sm table-secondary table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th id="search">Γενική Κατηγορία</th>
                <th id="search">Περιγραφή</th>
                <th>Ενέργειες</th>  
            </tr>
        </thead>
        <tbody>
            @foreach($action_types as $action_type)
                <tr>
                    <td>{{$action_type->category}}</td>
                    <td>{{$action_type->description}}</td>
                    <td>
                        <a href="{{route('actiontypes.edit', $action_type->id)}}" class="btn btn-primary">Επεξεργασία</a>
                        <form action="{{route('actiontypes.destroy', $action_type->id)}}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Διαγραφή</button>
                        </form>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div>
    <a href="{{route('actiontypes.create')}}" class="btn btn-success">Προσθήκη Εκπαιδευτικής Δράσης</a>
</div>
</x-layout_consultant>