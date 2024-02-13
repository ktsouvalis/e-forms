<x-layout_school>
    <div class="container">
        @push('title')
        <title>{{$fileshare->name}}</title>
        @endpush
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
            <div class="vstack gap-2">
            
                @php
                    $school = Auth::guard('school')->user();
                    $directory_common = '/fileshare'.$fileshare->id;
                    $directory_personal = $directory_common.'/personal_files';
                    $files_common=Storage::disk('local')->files($directory_common);
                    $files_personal = Storage::disk('local')->files($directory_personal);
                    $ffi = $fileshare->id           
                @endphp
                
                <hr>
                <strong>{{$fileshare->department->name}}: {{$fileshare->name}}</strong>
                <div class="hstack">
                    <div class="vstack gap-3">
                            @foreach($files_common as $file_c)
                                @php
                                    $basename = basename($file_c);
                                @endphp
                                <form action="{{url("/get_file/$fileshare->id/$basename")}}" method="post">
                                @csrf
                                    <input type="hidden" name="personal" value="0">
                                    <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου"> {{$basename}}</button>
                                </form>
                            @endforeach
                            @if($fileshare->allow_school)
                            <form action="{{url("/inform_my_teachers/$ffi")}}" method="post">
                                @csrf
                                <button type="submit" class="bi bi-info-circle btn btn-primary" onclick="return confirm('Επιβεβαίωση ενημέρωσης εκπαιδευτικών;')"> Ενημέρωση Εκπαιδευτικών Σχολείου για τα κοινά αρχεία</button>
                            </form>
                            @endif
                    </div>
                    <div class="vstack gap-2">
                            @foreach($files_personal as $file_p)
                                @if(strpos(basename($file_p), $school->code)!==false)
                                    @php
                                        $basename = basename($file_p);
                                    @endphp
                                    <form action="{{url("/get_file/$fileshare->id/$basename")}}" method="post">
                                    @csrf
                                        <input type="hidden" name="personal" value="1">
                                        <button class="btn btn-secondary bi bi-box-arrow-down" title="Λήψη αρχείου"> {{$basename}}</button>
                                    </form>
                                @endif
                            @endforeach
                    </div>
                </div>
                
            
            <hr>
            @if(session()->has('stakeholders_array'))
            @php
                $stakeholders_array = Session::pull('stakeholders_array', []);
            @endphp
            <table>
                <tr>
                    <th>Ενδιαφερόμενος</th>
                </tr>
            @foreach($stakeholders_array as $one)
            
            <tr>
                <td>{{$one['stakeholder']}}</td>
            </tr>
            @endforeach
            </table>
        @endif
                
                
            </div>
    </div>
</x-layout_school>