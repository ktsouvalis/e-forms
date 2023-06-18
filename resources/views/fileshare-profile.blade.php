<x-layout>
    <div class="container py-5">
        <div class="container px-5">
            <nav class="navbar navbar-light bg-light">
                <form action="{{url("/fileshare_save/$fileshare->id")}}" method="post" class="container-fluid" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="asks_to" value="insert">
                    <div class="input-group">
                        <span class="input-group-text w-25"></span>
                        <span class="input-group-text w-75"><strong>Επεξεργασία Διαμοιρασμού Αρχείων</strong></span>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon2">Name</span>
                        <input name="name" type="text" class="form-control" placeholder="Name" aria-label="Name" aria-describedby="basic-addon2" required value="{{$fileshare->name}}"><br>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon2">common files</span>
                        <input name="fileshare_common_files[]" type="file" class="form-control" multiple ><br>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text w-25" id="basic-addon2">personal files</span>
                        <input name="fileshare_personal_files[]" type="file" class="form-control" multiple><br>
                    </div>
                    
                    <input type="hidden" name="fileshare_id" value="{{$fileshare->id}}">
                    <div class="input-group">
                        <span class="w-25"></span>
                        <button type="submit" class="btn btn-primary bi bi-save m-2"> Αποθήκευση</button>
                        <a href="{{url("/fileshare_profile/$fileshare->id")}}" class="btn btn-outline-secondary bi bi-arrow-counterclockwise m-2"> Αναίρεση αλλαγών</a>
                    
                </form>
            </nav>
            @isset($dberror)
                <div class="alert alert-danger" role="alert">{{$dberror}}</div>
            @endisset
        </div>
        @php
        $getFiles = new App\Http\Controllers\FileshareController;
        $globalFiles = array();
        $globalFiles = $getFiles->getGlobalFilesToShow($fileshare->id);
        print_r($globalFiles);
        $personalFiles = array();
        @endphp
        <div class="row">
            <div class="col">
                Αρχεία κοινά για διαμοιρασμό
            </div>
            <div class="col">
                Αρχεία προσωπικά για διαμοιρασμό
            </div>
        </div>
    </div>    
</x-layout>