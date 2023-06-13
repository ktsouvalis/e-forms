<x-layout_teacher>
    @push('scripts')
    <script>
        function change_btn_state(){
            if(document.getElementById('inform').checked){
                document.getElementById('c_btn').disabled=false;
            }
            else{
                document.getElementById('c_btn').disabled=true;   
            }
        }
    </script>
    @endpush
    @php
        $teacher = Auth::guard('teacher')->user();
        $informed = App\Models\mEndDocument::where('teacher_id', $teacher->id)->get();
        $accepts = App\Models\Microapp::where('url', '/'.$appname)->first()->accepts;
        // dd($informed)
    @endphp
    <div class="container vstack gap-2">
        <div>

        </div>
        @if(!$accepts)
            <div class="col-sm-2 btn btn-warning bi bi-bricks rounded text-light" style="text-align:center;">
                Η εφαρμογή δε δέχεται υποβολές
            </div>
        @else
            <div >
            @if($informed->count()==0)
            <form action="{{url("/inform_end_documents/$teacher->id")}}" method="post">
                @csrf
                <div class="input-group">
                    <input type="checkbox" id="inform" name="inform" onChange="change_btn_state()">
                    <label for="inform" class="px-2"> Ενημερώνω τη Δι.Π.Ε. Αχαΐας ότι παρέλαβα τα αρχεία μου</label>
                </div>
                <div class="input-group py-2">
                    <button class="btn btn-primary bi bi-check-lg" id="c_btn" disabled> Επιβεβαίωση</button>
                </div>
                </form>
            @else
                <div class="col-sm-2 btn btn-success bi bi-check rounded text-light" style="text-align:center;">
                    Έχετε ενημερώσει ότι έχετε παραλάβει
                </div>   
            @endif
            </div>
        @endif
    </div>
</x-layout_teacher>