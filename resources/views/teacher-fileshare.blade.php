<x-layout_teacher>
    <div class="container">
        {{-- {{dd($teacher->fileshares)}} --}}
        @php
            $fileshares = $teacher->fileshares()->sortedByDepartment()->get();
            // dd($fileshares);
        @endphp
        @foreach($teacher->fileshares as $fileshare)
            @php
                $directory_common = 'public/file_shares/fileshare'.$fileshare->fileshare->id;
                $directory_personal = $directory_common.'/personal_files';
                $files_common=Storage::files($directory_common);
                $files_personal = Storage::files($directory_personal);
            @endphp
            <strong>{{$fileshare->fileshare->department->name}}: {{$fileshare->fileshare->name}}</strong>
            <div class="row">
                <div class="col">
                    <ul>
                        @foreach($files_common as $file_c)
                            <li><a href="{{Storage::url($file_c)}}">{{basename($file_c)}}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="col">
                    <ul>
                        @foreach($files_personal as $file_p)
                            @if(strpos(basename($file_p), $teacher->afm)!==false)
                                <li><a href="{{Storage::url($file_p)}}">{{basename($file_p)}}</a></li>
                            @endif
                        @endforeach
                    </ul>
                </div>
         </div>
        @endforeach
    </div>
</x-layout_teacher>