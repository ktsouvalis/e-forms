<x-layout>
    <div class="container py-5">
        <div class="vstack gap-5">
        <div class="container px-5">
            <nav class="navbar navbar-light bg-light">
                <form action="{{url("/fileshare_save/$fileshare->id")}}" method="post" class="container-fluid" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="asks_to" value="insert">
                    <div class="input-group">
                        <span class="input-group-text w-25"></span>
                        <span class="input-group-text w-75"><strong>Επεξεργασία Στοιχείων Διαμοιρασμού Αρχείων</strong></span>
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
                    
                    <div class="input-group">
                        <span class="w-25"></span>
                        <button type="submit" class="btn btn-primary bi bi-save m-2"> Αποθήκευση</button>
                        <a href="{{url("/fileshare_profile/$fileshare->id")}}" class="btn btn-outline-secondary bi bi-arrow-counterclockwise m-2"> Αναίρεση αλλαγών</a>
                    
                </form>
            </nav>
            
            
            
        </div>
        <div class="container px-5">
            <div>
                <div class="hstack gap-2">
                <form action="/import_whocan" method="post">
                    @csrf
                    <input type="hidden" name="my_app" value="fs">
                    <input type="hidden" name="my_id" value="{{$fileshare->id}}">
                    <button type="submit" class="btn btn-primary bi bi-person-lines-fill"> Εισαγωγή Stakeholders</button>
                </form>
                @if($fileshare->stakeholders->count())
                <form action="{{url("/delete_all_whocans")}}" method="post">
                    @csrf
                    <input type="hidden" name="my_app" value="fs">
                    <input type="hidden" name="my_id" value="{{$fileshare->id}}">
                    <button type="submit" class="btn btn-danger bi bi-x-circle"> Διαγραφή Stakeholders</button>
                </form>
                @endif
                </div>
            </div>
            <div class="table-responsive">
                <table  id="dataTable" class="display table table-sm table-striped table-hover">
                <thead>
                    <tr>
                        <th id="search">Αναγνωριστικό</th>
                        <th id="search">name</th>
                        <th> Διαγραφή</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($fileshare->stakeholders as $one_stakeholder)
                <tr>
                    @if($one_stakeholder->stakeholder_type=="App\Models\School")
                        <td>{{$one_stakeholder->stakeholder->code}}</td>
                    @else
                        <td>{{$one_stakeholder->stakeholder->afm}}</td>
                    @endif
                    <td>{{$one_stakeholder->stakeholder->surname}} {{$one_stakeholder->stakeholder->name}}</td>
                    <td> 
                        <form action="{{url('/delete_one_whocan')}}" method="post">
                            @csrf
                            <input type="hidden" name="my_app" value="fs">
                            <input type="hidden" name="my_id" value="{{$one_stakeholder->id}}">
                            <button type="submit" class="btn btn-danger bi bi-x-circle"> </button>
                        </form>
                    </td>
                </tr>
                @endforeach
                </tbody>
                </table>
            </div>  
        </div> 
        </div>
        
        @php
        
        $directory_common = 'file_shares/fileshares'.$fileshare->id;
        $directory_personal = $directory_common.'/personal_files';
        $files_common=Storage::files($directory_common);
        $files_personal = Storage::files($directory_personal);
       
        @endphp
        
        <div class="row">
            <div class="col">
                <strong>Αρχεία κοινά για διαμοιρασμό</strong>
                <ul>
                    @foreach($files_common as $file_c)
                        <li><a href="{{url('/storage/app/'.$file_c)}}">{{$file_c}}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="col">
               <strong>Αρχεία προσωπικά για διαμοιρασμό</strong>
                <ul>
                    @foreach($files_personal as $file_p)
                        <li><a href="{{url('/storage/app/'.$file_p)}}">{{$file_p}}</a></li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>    
</x-layout>