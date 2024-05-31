<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-8 pb-5">
    <div class="card border-primary rounded-0">
        <div class="card-header p-0">
            <div class="bg-info text-white text-center py-2">
                <h3><i class="fa-solid fa-user"></i> Προσωπικά Στοιχεία</h3>
                <p class="m-0"></p>
            </div>
        </div>
        <div class="card-body p-3">
            <!--Body-->
            <div class="form-group">
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><i class="fa-regular fa-user text-info"></i></div>
                    </div>
                    <div class="form-floating">
                        <input type="text" class="form-control" id="name_surname" name="name_surname" placeholder="" value="{{$teacher->surname}} {{$teacher->name}}" disabled>
                        <label for="name_surname">Ονοματεπώνυμο</label>
                    </div>
                    <div class="form-floating">
                        <input type="text" class="form-control" id="fathers_name" name="fathers_name" placeholder="" value={{$teacher->fname}} disabled>
                        <label for="fathers_name">Πατρώνυμο</label>
                    </div>
                </div>
            </div>
            {{-- Κλάδος και Αριθμός Μητρώου --}}
            <div class="form-group">
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><i class="fa-solid fa-person-chalkboard text-info"></i></div>
                    </div>
                    <div class="form-floating">
                        <input type="text" class="form-control" id="specialty" name="specialty" placeholder="" value={{$teacher->klados}} disabled>
                        <label for="specialty">Κλάδος</label>
                    </div>
            
                    <div class="form-floating">
                        <input type="text" class="form-control" id="am" name="am" placeholder="" value={{$teacher->am}} disabled>
                        <label for="am">Αριθμός Μητρώου</label>
                    </div>
                </div>
            </div>
            {{-- Οργανική Θέση και προϋπηρεσία --}}
            <div class="form-group">
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><i class="fa-solid fa-school-circle-check text-info"></i></div>
                    </div>
                    <div class="form-floating">
                        <input type="text" class="form-control" id="schoool" name="school" placeholder="Οργανική Θέση" value="{{$teacher->organiki->name}}" disabled>
                        <label for="school">Οργανική Θέση</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><i class="fa-solid fa-calendar-check text-info"></i></div>
                    </div>
                    <label for="years" class="px-2" >Προϋπηρεσία</label>
                        @if($teacher->work_experience)
                            <input type="text" class="form-control" id="years" name="years" placeholder="" value="{{$teacher->work_experience->years}} Έτη" disabled>
                            <input type="text" class="form-control" id="days" name="days" value="{{$teacher->work_experience->months}} Μήνες" disabled>
                            <input type="text" class="form-control" id="days" name="days" value="{{$teacher->work_experience->days}} Ημέρες" disabled>
                            <label for="days" class="px-2" >έως 31-8-2024</label>
                        @else
                            <input type="text" class="form-control" id="years" name="work_experience" placeholder="" value="Δεν έχει καταχωρηθεί προϋπηρεσία" disabled>
                        @endif
                        @if(isset($secondment))
                        <div class="form-group">
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="fa-solid fa-user-check text-info"></i></div>
                                </div>
                                <div class="input-group-text">Υπεύθυνες Δηλώσεις:</div>
                                <div class="form-check form-switch">    
                                    <input type="checkbox" class="form-check-input" id="statement_of_declaration" name="statement_of_declaration" @if($secondment->statement_of_declaration==1) checked @endif disabled>
                                    <label for="application_for_reposition" class="form-check-label" >Δεν έχω οριστεί στέλεχος εκπαίδευσης και ότι δεν υπηρετώ σε θέση με θητεία που λήγει μετά τις 31-08-2024.</label>
                                </div>
                                <div class="form-check form-switch"> 
                                    <input type="checkbox" class="form-check-input" id="application_for_reposition" name="application_for_reposition" @if($secondment->application_for_reposition==1) checked @endif disabled>
                                    <label for="application_for_reposition" class="form-check-label" >Έχω υποβάλλει αίτηση βελτίωσης θέσης / οριστικής τοποθέτησης το 2024</label>
                                </div>
                            </div>
                        </div>
                        @endif
                </div>
            </div>
        </div>  {{-- card body closure --}}
    </div> {{-- card closure --}}
    </div> {{-- col closure --}}
</div> {{-- row closure --}}
        