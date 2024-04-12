<x-layout_teacher>
    @php
        //check which teacher is logged in
        $teacher = Auth::guard('teacher')->user();
         // $microapp = App\Models\Microapp::where('url', '/'.$appname)->first();
       // $accepts = $microapp->accepts; //fetch microapp 'accepts' field
       // $outings = $school->outings;
    @endphp
    @push('links')
        <link href="{{asset('DataTables-1.13.4/css/dataTables.bootstrap5.css')}}" rel="stylesheet"/>
        <link href="{{asset('Responsive-2.4.1/css/responsive.bootstrap5.css')}}" rel="stylesheet"/>
    @endpush
    @push('scripts')
        <script src="{{asset('DataTables-1.13.4/js/jquery.dataTables.js')}}"></script>
        <script src="{{asset('DataTables-1.13.4/js/dataTables.bootstrap5.js')}}"></script>
        <script src="{{asset('Responsive-2.4.1/js/dataTables.responsive.js')}}"></script>
        <script src="{{asset('Responsive-2.4.1/js/responsive.bootstrap5.js')}}"></script>
        <script src="{{asset('datatable_init.js')}}"></script>
    @endpush
    @push('title')
        <title>Αποσπάσεις</title>
    @endpush
<div class="container">
    <h2 class="text-center">Αίτηση Απόσπασης εντός ΠΥΣΠΕ Αχαΐας</h2>
	<div class="row justify-content-center">
		<div class="col-12 col-md-8 col-lg-8 pb-5">
        <div class="card border-primary rounded-0">
            <div class="card-header p-0">
                <div class="bg-info text-white text-center py-2">
                    <h3><i class="fa-solid fa-user"></i> Προσωπικά Στοιχεία</h3>
                    <p class="m-0">Παρακαλούμε ελέγξτε την ορθότητα των στοιχείων που αναγράφονται</p>
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
                            <input type="text" class="form-control" id="name_surname" name="name_surname" placeholder="" value={{$teacher->surname}} {{$teacher->name}} disabled>
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
                            <input type="text" class="form-control" id="schoool" name="school" placeholder="Οργανική Θέση" value={{$teacher->organiki->name}} disabled>
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
                            <input type="text" class="form-control" id="years" name="years" placeholder="" value="17 Έτη" disabled>
                            
                            <input type="text" class="form-control" id="days" name="days" value="5 Μήνες" disabled>
                            <input type="text" class="form-control" id="days" name="days" value="24 Ημέρες" disabled>
                            <label for="days" class="px-2" >έως 31-8-2024</label>
                        
                    </div>
                </div>
            </div>
            
        </div>
        </div>

        
	</div>

    {{-- Δηλώσεις - Α Τμήμα Αίτησης --}}
    <div class="row justify-content-center">
		<div class="col-12 col-md-8 col-lg-8 pb-5">
        <div class="card border-primary rounded-0">
            <div class="card-header p-0">
                <div class="bg-info text-white text-center py-2">
                    <h3><i class="fa-regular fa-square-check"></i> Δηλώσεις</h3>
                    <p class="m-0">Παρακαλούμε υποβάλλετε τις ακόλουθες δηλώσεις προκειμένου να προχωρήσετε σε δήλωση των μοριοδοτούμενων κριτηρίων</p>
                </div>
            </div>
            <div class="card-body p-3">
            <form action="{{route('secondments.store')}}" method="post">
                @csrf
                <!--Body-->
                <div class="form-group">
                    
                    <div class="input-group mb-2">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="statement_of_declaration">
                            <label class="form-check-label" for="flexSwitchCheckDefault">* Δηλώνω υπεύθυνα ότι δεν έχω οριστεί στέλεχος εκπαίδευσης (λ.χ. προϊστάμενος/μένη ολιγοθέσιας σχολικής μονάδας, διευθυντής/ντρια σχολ. μονάδας) και ότι δεν υπηρετώ σε θέση με θητεία που λήγει μετά τις 31-08-2024</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group mb-2">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault">
                            <label class="form-check-label" for="flexSwitchCheckDefault">Έχω υποβάλλει αίτηση βελτίωσης θέσης / οριστικής τοποθέτησης το 2024</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group mb-2">
                        <div class="form-check form-switch">
                            <input type="hidden" name="special_needs_position" value="0">
                            <input class="form-check-input" type="checkbox" name="special_needs_position" value="1" id="special_needs_position_checked" @if($teacher->org_eae == 1) checked @endif>
                            <label class="form-check-label" for="post_graduate_studies">Έχω οργανική θέση στην Ειδική Αγωγή</label>
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <input type="submit" value="Υποβολή" class="btn btn-info btn-block rounded-2 py-2">
                </div>
            </form>
            </div>
            <small><em> * Υποχρεωτική δήλωση προκειμένου να προχωρήσετε σε δημιουργία αίτησης.</em></small>
            
        </div>
        </div>

        
	</div>
</div>

</x-layout_teacher>