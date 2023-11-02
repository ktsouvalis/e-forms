<x-layout_school>
    <div class="container">
        @push('title')
        <title>Αρχεία Διεύθυνσης</title>
        @endpush
        @php
            $fileshares = $school->fileshares()->get();
        @endphp
        @if(!$fileshares->count())
            <div class='container container-narrow'>
                <div class='alert alert-info text-center'>
                    Σε αυτή την ενότητα θα βρίσκετε αρχεία που κάποιο από τα Τμήματα της Διεύθυνσης, μοιράστηκε μαζί σας. <br>
                    <strong>Αυτή τη στιγμή δεν υπάρχουν αρχεία για το σχολείο σας.</strong>
                </div>
            </div>
           
        @else
            <div class="vstack gap-2">
            @foreach($school->fileshares as $fileshare)
                @php
                    $directory_common = '/fileshare'.$fileshare->fileshare->id;
                    $directory_personal = $directory_common.'/personal_files';
                    $files_common=Storage::disk('local')->files($directory_common);
                    $files_personal = Storage::disk('local')->files($directory_personal);
                @endphp
                
                <hr>
                <strong>{{$fileshare->fileshare->department->name}}: {{$fileshare->fileshare->name}}</strong>
                <div class="hstack">
                    <div class="vstack gap-2">
                            @foreach($files_common as $file_c)
                                @php
                                    $ffi = $fileshare->fileshare->id
                                @endphp
                                <form action="{{url("/dl_file/$ffi")}}" method="post">
                                @csrf
                                    <input type="hidden" name="filename" value="{{$file_c}}">
                                    <button class="btn btn-secondary bi bi-box-arrow-down"> {{basename($file_c)}}</button>
                                </form>
                            @endforeach
                    </div>
                    <div class="vstack gap-2">
                            @foreach($files_personal as $file_p)
                                @if(strpos(basename($file_p), $school->code)!==false)
                                    @php
                                        $ffi = $fileshare->fileshare->id
                                    @endphp
                                    <form action="{{url("/dl_file/$ffi")}}" method="post">
                                    @csrf
                                        <input type="hidden" name="filename" value="{{$file_p}}">
                                        <button class="btn btn-secondary bi bi-box-arrow-down"> {{basename($file_p)}}</button>
                                    </form>
                                @endif
                            @endforeach
                    </div>
                </div>
                
            @endforeach
            </div>
    @endif
    </div>
</x-layout_school>