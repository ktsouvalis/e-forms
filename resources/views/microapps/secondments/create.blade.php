<x-layout_teacher>
    @php
        //check which teacher is logged in
        $teacher = Auth::guard('teacher')->user();
        $microapp = App\Models\Microapp::where('url', '/secondments')->first();
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
    @if($microapp->accepts == 0)
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            Η υποβολή αιτήσεων δεν είναι ενεργή αυτή τη στιγμή.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <h2 class="text-center">Αίτηση Απόσπασης εντός ΠΥΣΠΕ Αχαΐας</h2>
	@include('microapps.secondments.inc_personal_data')
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
                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="application_for_reposition">
                            <label class="form-check-label" for="flexSwitchCheckDefault">Έχω υποβάλλει αίτηση βελτίωσης θέσης / οριστικής τοποθέτησης το 2024</label>
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <input type="submit" value="Υποβολή" class="btn btn-info btn-block rounded-2 py-2" @if($microapp->accepts == 0) disabled @endif>
                </div>
            </form>
            </div>
            <small><em> * Υποχρεωτική δήλωση προκειμένου να προχωρήσετε σε δημιουργία αίτησης.</em></small>
            
        </div>
        </div>

        
	</div>
</div>

</x-layout_teacher>