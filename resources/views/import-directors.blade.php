<x-layout>
    <div class="container">
        <strong>ΠΡΟΣΟΧΗ!! ΑΠΟ ΤΟ 4.25 του myschool πρέπει να σβηστεί η 1 από τις 2 εγγραφές των σχολείων που παρουσιάζονται 2 φορές ΚΑΙ έχουν ΝΑΙ στον αναπληρωτή διευθυντή. Επίσης, πρέπει να διαγραφούν τα ιδιωτικά σχολεία.</strong>
        @if(!session()->has('asks_to'))
        <nav class="navbar navbar-light bg-light">
            <form action="{{url('/upload_directors_template')}}" method="post" class="container-fluid" enctype="multipart/form-data">
                @csrf
                <input type="file" name="import_directors" >    
                <button type="submit" class="btn bi bi-filetype-xlsx btn-primary"> Αποστολή αρχείου</button>
            </form>
        </nav>
        @else
        
        <div style="p-3 mb-2 bg-info text-dark">
            Διαβάστηκαν:
        </div>
        <div class="table-responsive">
        <table class="table table-striped table-hover table-light">
            <tr>
                <th id="search">Κωδικός Σχολείου</th>
                <th id="search">Ονομασία</th>
                <th id="search">ΑΦΜ Διευθυντή</th>
                <th id="search">Ονοματεπώνυμο Διευθυντή</th>
            </tr>
            @php
                $row = 2;
            @endphp
            @foreach(session('directors_array') as $director)
                <tr>  
                    <td>{{$director['code']}}</td>
                    <td>{{$director['school_name']}}</td>
                    <td>{{$director['afm']}}</td>
                    <td>{{$director['director_surname']}}</td>
            @endforeach
        </table>
        </div>
        @if(session('asks_to')=='save')
            <div class="row">
                <form action="{{url('/preview_directors')}}" method="get" class="col container-fluid" enctype="multipart/form-data">
                @csrf
                    <button type="submit" class="btn btn-primary bi bi-search"> Προεπισκόπηση</button>
                </form>
                <a href="{{url('/directors')}}" class="col">Ακύρωση</a>
            </div>
        @else
            <div class="row">
                <div>
                    Διορθώστε τα σημειωμένα σφάλματα και υποβάλετε εκ νέου το αρχείο.
                </div>
                <a href="{{url('/directors')}}" class="col">Ακύρωση</a>
            </div>
        @endif
    @endif  
    </div>
</x-layout>


