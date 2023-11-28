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
        
        <hr>
        <strong>{{$fileshare->department->name}}: {{$fileshare->name}}</strong>
        <div class="hstack">
            <div class="vstack gap-2">
                    @foreach($files_common as $file_c)
                        <form action="{{url("/dl_file/$ffi")}}" method="post">
                        @csrf
                            <input type="hidden" name="filename" value="{{$file_c}}">
                            <button class="btn btn-secondary bi bi-box-arrow-down"> {{basename($file_c)}}</button>
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
                    @if(!empty($fieldOfInterest) && strpos(basename($file_p), $fieldOfInterest)!==false)
                        <form action="{{url("/dl_file/$ffi")}}" method="post">
                        @csrf
                            <input type="hidden" name="filename" value="{{$file_p}}">
                            <button class="btn btn-secondary bi bi-box-arrow-down"> {{basename($file_p)}}</button>
                        </form>
                    @endif
                @endforeach
            </div>
        </div> <!-- hStack closure -->
        </div> <!-- vStack gap-2 closure -->
    </div> <!-- container closure -->
</x-layout_teacher>