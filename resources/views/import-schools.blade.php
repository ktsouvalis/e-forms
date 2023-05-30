<x-layout>
    <div class="container">
        @if(!session()->has('asks_to'))
        <nav class="navbar navbar-light bg-light">
            <form action="{{url('/upload_schools_template')}}" method="post" class="container-fluid" enctype="multipart/form-data">
                @csrf
                <input type="file" name="import_schools" >    
                <button type="submit" class="btn bi bi-filetype-xlsx btn-primary"> Αποστολή αρχείου</button>
            </form>
        </nav>
        @else
        
        <div style="p-3 mb-2 bg-info text-dark">
            Διαβάστηκαν:
        </div>
        
        <table class="table table-striped table-hover table-light">
            <tr>
                <th id="search">Κωδικός</th>
                <th id="search">Ονομασία</th>
                <th id="search">email</th>
                <th id="search">link</th>
                <th id="search">Δημ/Νηπ</th>
                <th id="search">Δήμος</th>
                <th id="search">Ειδικό</th>
                <th id="search">Δημόσιο</th>
            </tr>
            @php
                $row = 2;
            @endphp
            @foreach(session('schools_array') as $school)
                {{-- @if(!null==session("errors_array[$row]")) --}}
                <tr>  
                    <td>{{$school['code']}}</td>
                    <td>{{$school['name']}}</td>
                    <td>{{$school['mail']}}</td>
                    <td>{{$school['md5']}}</td>
                    <td>{{$school['primary']}}</td>
                    @if($school['municipality']!="")
                        @php   
                            $municipality = App\Models\Municipality::find($school['municipality']);   
                        @endphp
                        <td >{{$municipality->name}}</td>
                    @else
                        <td style='color: red'> Αγνωστος Δήμος </td>
                    @endif
                    <td>{{$school['special_needs']}}</td>
                    <td>{{$school['international']}}</td>
                    
            @endforeach
        </table>

        @if(session('asks_to')=='save')
            <div class="row">
                <form action="{{url('/preview_schools')}}" method="get" class="col container-fluid" enctype="multipart/form-data">
                @csrf
                    <button type="submit" class="btn btn-primary bi bi-search"> Προεπισκόπηση</button>
                </form>
                <a href="{{url('/schools')}}" class="col">Ακύρωση</a>
            </div>
        @else
            <div class="row">
                <div>
                    Διορθώστε τα σημειωμένα σφάλματα και υποβάλετε εκ νέου το αρχείο.
                </div>
                <a href="{{url('/schools')}}" class="col">Ακύρωση</a>
            </div>
        @endif
    @endif  
    </div>
</x-layout>


