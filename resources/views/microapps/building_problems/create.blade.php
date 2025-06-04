
<x-layout_school>
    @php
        $school = Auth::guard('school')->user(); //check which school is logged in
        $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
        $accepts = $microapp->accepts; 
        $name = $microapp->name;
        $building_problems = $school->building_problems;
        
    @endphp
    
    @push('title')
        <title>{{$name}}</title>
    @endpush
    <div class="container">
        <div class="container px-5">
            <nav class="navbar navbar-light bg-light">
                <div class="input-group">
                    <span class="input-group-text w-50 text-wrap">Παρατηρήσεις</span>
                    <textarea name="comments" id="comments" class="form-control" cols="30" rows="2" style="resize: none;" >@if($building_problems){{$building_problems->comments}}@endif</textarea>
                </div>
                        <div class="mt-4">
                            <label class="form-label fw-bold mb-2">Συνολικός Βαθμός Αξιολόγησης:</label>
                            <div class="d-flex flex-wrap gap-3">
                                @php
                                    $choices = [
                                        '0',
                                        '1',
                                        '2',
                                        '3',
                                        '4',
                                        '5'
                                    ];
                                    $selected = $building_problems ? $building_problems->severity : '';
                                @endphp
                                
                                @foreach($choices as $index => $choice)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="problem_type" id="problem_type_{{$index}}" value="{{$choice}}" {{ $selected == $choice ? 'checked' : '' }}>
                                        <label class="form-check-label" for="problem_type_{{$index}}">
                                            {{$choice}}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <div class= "form-text text-muted mt-2">
                                <strong>Επεξήγηση:</strong>
                                <br>0 - Δεν υπάρουν προβλήματα, 
                                <br>1 - Ελαφριά προβλήματα,
                                <br>2 - Μέτρια προβλήματα,
                                <br>3 - Σημαντικά προβλήματα,
                                <br>4 - Σοβαρά προβλήματα,
                                <br>5 - Πολύ σοβαρά προβλήματα
                            </div>
                        </div>
                        <div class="col-12 col-md-8 col-lg-8 pb-5">
        <div class="card border-primary rounded-0">
            <div class="card-header p-0">
                <div class="bg-info text-white text-center py-2">
                    <h3><i class="fa-regular fa-file-lines"></i> Υποβολή Δικαιολογητικών</h3>
                    <p class="m-0">Υποβάλλονται δικαιολογητικά σε μορφή .pdf, .jpeg .png < 10MB ανά υποβολή</p>
                </div>
            </div>
            
            <div class="card-body p-3">
                <div class="row justify-content-right">
                    <div class="text-center py-2">
                        <p class="m-0">Μπορείτε να επιλέξετε και να ανεβάσετε και περισσότερα από ένα αρχεία ταυτόχρονα.</p>
                    </div>
                    <form action="{{route('building_problems.upload_files', ['building_problems' => $building_problems])}}" method="post" class="container-fluid" enctype="multipart/form-data">
                        @csrf
                        <div class="text-center">
                            <input  type="file" id="files" name="files[]" multiple required @if(($building_problems->criteria_submitted == 1 && $building_problems->extra_files_allowed == 0) || ($microapp->accepts == 0 && $building_problems->extra_files_allowed == 0)) disabled @endif>
                            <input type="submit" value="Ανέβασμα" class="btn btn-info btn-block rounded-2 py-2"
                            @if(($building_problems->criteria_submitted == 1 && $building_problems->extra_files_allowed == 0) || ($microapp->accepts == 0 && $building_problems->extra_files_allowed == 0)) disabled @endif >
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="row justify-content-right">
                    <div class="text-center py-2">
                        <p class="m-0">Αρχεία που έχουν υποβληθεί:</p>
                        @if($building_problems->files_json)
                            @php 
                                $count = 1;
                                $fileNames = json_decode($building_problems->files_json, true);
                            @endphp
                            @foreach($fileNames as $serverFileName => $databaseFileName)
                            
                            <div class="d-flex justify-content-between">
                                <form action="{{route('building_problems.download_file', ['serverFileName' => $serverFileName, 'databaseFileName' => $databaseFileName])}}" method="get">
                                    <input type="submit" class="btn btn-info btn-block rounded-2 py-2 m-1" value="{{$databaseFileName}}" >
                                </form>
                                <form action="{{route('building_problems.delete_file', [ 'building_problems' => $building_problems, 'serverFileName' => $serverFileName ])}}" method="get">
                                    <input type="submit" class="btn btn-danger btn-block rounded-3" value="Διαγραφή" 
                                    @if($building_problems->criteria_submitted == 1 || $microapp->accepts == 0) disabled @endif >
                                </form>
                            </div>
                            @php $count++; @endphp
                            @endforeach
                        @else
                            <p class="m-0">Δεν έχει υποβληθεί κάποιο αρχείο</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
            </nav>
        </div>
    </div>

</x-layout_school>