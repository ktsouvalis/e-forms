<x-layout_teacher>
    @push('title')
        <title>{{$fileshare->name}}</title>
    @endpush
    <div class="container">
        @php
            $teacher = Auth::guard('teacher')->user();
        @endphp
        <div class="vstack gap-2">
        
        @php
            $directory_common = '/fileshare'.$fileshare->id;
            $directory_personal = $directory_common.'/personal_files';
            $files_common=Storage::disk('local')->files($directory_common);
            $files_personal = Storage::disk('local')->files($directory_personal);
            $ffi = $fileshare->id
        @endphp
        
        
        <strong>{{$fileshare->department->name}}: {{$fileshare->name}}</strong>
        @if($fileshare->comment)
            <div class="row">
                <div class="col"></div>
                <div class="col">
                <div class="card py-2" style="background-color: Gainsboro; text-decoration: none; font-size: small">
                    <div class="m-1 post-text">{!!html_entity_decode($fileshare->comment)!!}</div>
                </div>
                </div>
                <div class="col"></div>
            </div>
            
        @endif
        <div class="hstack">
            <div class="vstack gap-2">
                    @foreach($files_common as $file_c)
                        @php
                            $basename = basename($file_c);
                        @endphp
                        <form action="{{url("/get_file/$fileshare->id/$basename")}}" method="post">
                        @csrf
                            <input type="hidden" name="personal" value="0">
                            <button class="btn btn-secondary bi bi-box-arrow-down"> {{$basename}}</button>
                        </form>
                    @endforeach
            </div>
            <div class="vstack gap-2">
                @foreach($files_personal as $file_p)
                    @php
                        $string = basename($file_p); 
                        
                        // Regular expression for a 6-digit number
                        $regex6 = '/(?<!\d)\d{6}(?!\d)/';

                        // Regular expression for a 9-digit number
                        $regex9 = '/(?<!\d)\d{9}(?!\d)/';
                        $fieldOfInterest = '';
                        
                        if (preg_match($regex9, $string)) {
                            $fieldOfInterest = $teacher->afm;//the number is afm
                        } 
                        else if (preg_match($regex6, $string)) {
                            $fieldOfInterest = $teacher->am;//else the number is am
                        }
                    @endphp
                    @if(!empty($fieldOfInterest) && strpos($string, $fieldOfInterest)!==false)
                        <form action="{{url("/get_file/$fileshare->id/$string")}}" method="post">
                        @csrf
                            <input type="hidden" name="personal" value="1">
                            <button class="btn btn-secondary bi bi-box-arrow-down"> {{$string}}</button>
                        </form>
                    @endif
                @endforeach
            </div>
        </div> <!-- hStack closure -->
        </div> <!-- vStack gap-2 closure -->
    </div> <!-- container closure -->
</x-layout_teacher>