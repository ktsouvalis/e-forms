
<x-layout_school>
    @php
        
        
        function formatDeadline($deadline) {
            if (!$deadline) return '';
            $date = \Carbon\Carbon::parse($deadline);
            return $date->format('d/m/Y');
        }
        
        
        // List of microapps without deadline
        $noDeadlineMicroapps = ['tickets', 'outings', 'timetables'];
    @endphp

    @push('title')
        <title>Καρτέλα {{auth('school')->user()->name ?? 'Σχολείου'}}</title>
    @endpush

    <!-- Add Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-hover:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        .status-pulse {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .glass-effect {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
        }
        .compact-card {
            height: 180px; /* Smaller height for compact cards */
        }
        .compact-card .card-icon {
            width: 50px;
            height: 50px;
        }
        .compact-card .card-title {
            font-size: 1rem;
        }
    </style>

    <body>
    
    @auth('school')
        @php 
            $school = Illuminate\Support\Facades\Auth::guard('school')->user();
            $active_microapp = false;
            if($school->microapps->count()){
                foreach($school->microapps as $microapp){
                    if($microapp->microapp->visible){
                        $active_microapp = true;
                        break;
                    }
                } 
            }
            
            $active_filecollect = false;
            if($school->filecollects->count()){
                foreach($school->filecollects as $filecollect){
                    if($filecollect->filecollect->visible){
                        $active_filecollect = true;
                        break;
                    }
                } 
            }
            
            // Separate microapps with and without deadlines
            $microappsWithDeadline = [];
            $microappsWithoutDeadline = [];
            
            foreach ($school->microapps as $one_microapp) {
                if ($one_microapp->microapp->visible) {
                    if (in_array($one_microapp->microapp->url, ['/tickets', '/outings', '/timetables']) || 
                        empty($one_microapp->microapp->closes_at)) {
                        $microappsWithoutDeadline[] = $one_microapp;
                    } else {
                        $microappsWithDeadline[] = $one_microapp;
                    }
                }
            }
        @endphp

        <!-- Header -->
        <div class="text-dark py-8 mb-8 shadow-lg" style="background: #74c8dfff; backdrop-filter: blur(10px);">
            <div class="container mx-auto px-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold mb-2">
                            <i class="fas fa-school mr-3"></i>
                            {{$school->name}}
                        </h1>
                        <p class="text-blue-100 text-lg">Ηλεκτρονικές Φόρμες</p>
                    </div>
                    <div class="text-right">
                        <div class="text-blue-100 text-sm">Σήμερα</div>
                        <div class="text-xl">{{ now()->format('d/m/Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="container mx-auto px-6 pb-8">
            @if($active_microapp or $active_filecollect)
                <!-- Microapps without deadline (compact section) -->
                @if(count($microappsWithoutDeadline) > 0)
                <div class="mb-8">
                    <div class="text-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Υποβολή Σχολείου</h2>
                        <p class="text-gray-600">Αιτήματα που υποβάλλει το Σχολείο για βοήθεια ή έγκριση και δεν έχουν συγκεκριμένη προθεσμία.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
                        @foreach ($microappsWithoutDeadline as $one_microapp)
                            @php 
                                $resource = substr($one_microapp->microapp->url, 1);
                                $submissionExists = false; // Assuming no submission exists for no deadline microapps
                                $status = App\Http\Controllers\SchoolController::getSubmissionStatus($one_microapp->microapp, $submissionExists);
                            @endphp
                            <div class="card-hover">
                                <div class="bg-white rounded-lg shadow-sm border border-gray-200 compact-card relative overflow-hidden">
                                    <a href="{{route("$resource.create")}}" class="block h-full">
                                        <div class="p-4 h-full flex flex-col">
                                            <!-- Icon and Title Section -->
                                            <div class="text-center mb-4">
                                                <div class="{{$status['color']}} rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4 shadow-lg" style="background: #868484ff">
                                                    <i class="{{$one_microapp->microapp->icon}} text-3xl {{$status['text']}}"></i>
                                                </div>
                                                <!-- <div class="rounded-full card-icon flex items-center justify-center mx-auto mb-3 shadow" style="background: #868484ff">
                                                    <i class="{{$one_microapp->microapp->icon}} text-xl {{$status['text']}}"></i>
                                                </div> -->
                                                <h2 class="text-xl font-semibold text-gray-800 mb-2 hover:text-blue-600 transition-colors">
                                                    {{$one_microapp->microapp->name}}
                                                </h2>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Submissions Section with Deadlines -->
                <div class="mb-12">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-gray-800 mb-2">Υποβολή Στοιχείων</h2>
                        <p class="text-gray-600">Στοιχεία που ζητά η Διεύθυνση με προθεσμία.</p>
                    </div>

                    <!-- Cards Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach ($microappsWithDeadline as $one_microapp)
                            @php 
                                $resource = substr($one_microapp->microapp->url, 1);
                                $submissionExists = App\Http\Controllers\SchoolController::getSubmissionExists($one_microapp->microapp, $school);
                                $status = App\Http\Controllers\SchoolController::getSubmissionStatus($one_microapp->microapp, $submissionExists);
                                //dd($school->internal_rule);
                            @endphp
                            <div class="card-hover h-100">
                                <div class="bg-white rounded-lg shadow-sm border border-gray-200 h-full relative overflow-hidden">
                                    <!-- Status Badge -->
                                    <div class="absolute top-4 right-4 z-10">
                                        <span class="{{$status['color']}} {{$status['text']}} px-3 py-1 rounded-full text-xs font-semibold status-pulse">
                                            @php
                                                // Special case for no-deadline microapps
                                                if ($one_microapp->microapp->url == '/internal_rules') { // έχει υπογραφεί και από τους δύο;
                                                    if($submissionExists){
                                                        if($school->internal_rule->consultant_signed_file && $school->internal_rule->director_signed_file){
                                                        echo 'Ολοκληρώθηκε';
                                                    } elseif(!$school->internal_rule->consultant_signed_file || !$school->internal_rule->director_signed_file) { // δεν έχει ολοκληρωθεί και κάποιος ζητά διόρθωση
                                                        if($school->internal_rule->approved_by_director && $school->internal_rule->approved_by_consultant){
                                                            echo 'Αναμονή Υπογραφών';
                                                        } else if($school->internal_rule->consultant_comments_file || $school->internal_rule->director_comments_file){
                                                            echo 'Αναμονή Διόρθωσης';
                                                        }
                                                    } else {
                                                        echo 'Εκκρεμεί';
                                                    }
                                                    }
                                                    
                                                } else {
                                                    echo $status['badge'];
                                                }
                                            @endphp
                                        </span>
                                    </div>

                                    <a href="{{route("$resource.create")}}" class="block h-full">
                                        <div class="p-6 h-full flex flex-col">
                                            <!-- Icon and Title Section -->
                                            <div class="text-center mb-6">
                                                <div class="{{$status['color']}} rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4 shadow-lg">
                                                    <i class="{{$one_microapp->microapp->icon}} text-3xl {{$status['text']}}"></i>
                                                </div>
                                                <h3 class="text-xl font-semibold text-gray-800 mb-2 hover:text-blue-600 transition-colors">
                                                    {{$one_microapp->microapp->name}}
                                                </h3>
                                            </div>
                                            
                                            <!-- Deadline Information -->
                                            @if(isset($one_microapp->microapp->closes_at))
                                                <div class="border-t border-gray-100 pt-4 mt-auto">
                                                    <div class="space-y-3">
                                                        <div class="flex items-center justify-between text-sm">
                                                            <span class="text-gray-500 flex items-center">
                                                                <i class="fas fa-calendar-alt mr-2"></i>
                                                                Προθεσμία:
                                                            </span>
                                                            <span class="font-semibold text-gray-700">{{formatDeadline($one_microapp->microapp->closes_at)}}</span>
                                                        </div>
                                                        <div class="flex items-center justify-between text-sm">
                                                            <span class="text-gray-500 flex items-center">
                                                                <i class="fas fa-clock mr-2"></i>
                                                                Κατάσταση:
                                                            </span>
                                                            <span class="font-semibold {{$status['status'] === 'overdue' ? 'text-red-600' : ($status['status'] === 'urgent' ? 'text-orange-600' : 'text-gray-700')}}">
                                                                {{App\Http\Controllers\SchoolController::getDaysRemaining($one_microapp->microapp->closes_at)}}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Progress indicator -->
                                            <div class="{{isset($one_microapp->microapp->closes_at) ? 'mt-4' : 'mt-auto'}} text-center">
                                                @if($submissionExists)
                                                    <div class="flex items-center justify-center text-green-600 text-sm font-semibold bg-green-50 py-2 px-4 rounded-lg">
                                                        <i class="fas fa-check-circle mr-2"></i>
                                                        Ολοκληρώθηκε
                                                    </div>
                                                @else
                                                    <div class="flex items-center justify-center text-gray-500 text-sm bg-gray-50 py-2 px-4 rounded-lg">
                                                        <i class="fas fa-clock mr-2"></i>
                                                        Εκκρεμεί
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endforeach

                        @foreach($school->filecollects as $filecollect)
                            @if($filecollect->filecollect->visible)
                                @php
                                    $ffi = $filecollect->filecollect->id;
                                    $submissionExists = App\Http\Controllers\SchoolController::getSubmissionExists($filecollect, $school);
                                    $status = App\Http\Controllers\SchoolController::getSubmissionStatus($filecollect->filecollect, $submissionExists);
                                @endphp
                                <div class="card-hover h-100">
                                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 h-full relative overflow-hidden">
                                        <!-- Status Badge -->
                                        <div class="absolute top-4 right-4 z-10">
                                            <span class="{{$status['color']}} {{$status['text']}} px-3 py-1 rounded-full text-xs font-semibold status-pulse">
                                                {{$status['badge']}}
                                            </span>
                                        </div>

                                        <a href="{{url("/filecollects/$ffi")}}" class="block h-full">
                                            <div class="p-6 h-full flex flex-col">
                                                <!-- Icon and Title Section -->
                                                <div class="text-center mb-6">
                                                    <div class="{{$status['color']}} rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4 shadow-lg">
                                                        <i class="fa-solid fa-file-pdf text-3xl {{$status['text']}}"></i>
                                                    </div>
                                                    <h3 class="text-xl font-semibold text-gray-800 mb-2 hover:text-blue-600 transition-colors">
                                                        {{$filecollect->filecollect->name}}
                                                    </h3>
                                                </div>
                                                
                                                <!-- Deadline Information -->
                                                @if(isset($filecollect->filecollect->closes_at))
                                                    <div class="border-t border-gray-100 pt-4 mt-auto">
                                                        <div class="space-y-3">
                                                            <div class="flex items-center justify-between text-sm">
                                                                <span class="text-gray-500 flex items-center">
                                                                    <i class="fas fa-calendar-alt mr-2"></i>
                                                                    Προθεσμία:
                                                                </span>
                                                                <span class="font-semibold text-gray-700">{{formatDeadline($filecollect->filecollect->closes_at)}}</span>
                                                            </div>
                                                            <div class="flex items-center justify-between text-sm">
                                                                <span class="text-gray-500 flex items-center">
                                                                    <i class="fas fa-clock mr-2"></i>
                                                                    Κατάσταση:
                                                                </span>
                                                                <span class="font-semibold {{$status['status'] === 'overdue' ? 'text-red-600' : ($status['status'] === 'urgent' ? 'text-orange-600' : 'text-gray-700')}}">
                                                                    {{App\Http\Controllers\SchoolController::getDaysRemaining($filecollect->filecollect->closes_at)}}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Progress indicator -->
                                                <div class="{{isset($filecollect->filecollect->closes_at) ? 'mt-4' : 'mt-auto'}} text-center">
                                                    @if($submissionExists)
                                                        <div class="flex items-center justify-center text-green-600 text-sm font-semibold bg-green-50 py-2 px-4 rounded-lg">
                                                            <i class="fas fa-check-circle mr-2"></i>
                                                            Ολοκληρώθηκε
                                                        </div>
                                                    @else
                                                        <div class="flex items-center justify-center text-gray-500 text-sm bg-gray-50 py-2 px-4 rounded-lg">
                                                            <i class="fas fa-upload mr-2"></i>
                                                            Εκκρεμεί αρχείο
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            @if(!(count($school->fileshares)==0))
                <!-- Document Retrieval Section -->
                <div class="mb-12">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-gray-800 mb-2">Παραλαβή Εγγράφων</h2>
                        <p class="text-gray-600">Έγγραφα διαθέσιμα για λήψη</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($school->fileshares as $fileshare)
                            @php
                                $ffi = $fileshare->fileshare->id;
                            @endphp
                            <div class="card-hover h-100">
                                <div class="bg-white rounded-lg shadow-sm border border-gray-200 h-full">
                                    <a href="{{url("/fileshares/$ffi")}}" class="block h-full">
                                        <div class="p-6 h-full flex flex-col">
                                            <!-- Icon and Title Section -->
                                            <div class="text-center mb-6">
                                                <div class="bg-cyan-500 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4 shadow-lg">
                                                    <i class="fa-solid fa-file-pdf text-3xl text-white"></i>
                                                </div>
                                                <h3 class="text-xl font-semibold text-gray-800 mb-2 hover:text-cyan-600 transition-colors">
                                                    {{$fileshare->fileshare->name}}
                                                </h3>
                                            </div>
                                            
                                            <!-- Status Section -->
                                            <div class="mt-auto text-center">
                                                <div class="flex items-center justify-center text-cyan-600 text-sm font-semibold bg-cyan-50 py-2 px-4 rounded-lg">
                                                    <i class="fas fa-download mr-2"></i>
                                                    Διαθέσιμο για λήψη
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Logout Section -->
            <div class="text-center">
                <div class="max-w-sm mx-auto">
                    <div class="card-hover">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            <a href="{{url('/slogout')}}" class="block p-6 text-center hover:bg-red-50 transition-colors rounded-lg">
                                <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 hover:bg-red-100 transition-colors">
                                    <i class="fa-solid fa-arrow-right-from-bracket text-2xl text-gray-600 hover:text-red-600 transition-colors"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800 hover:text-red-600 transition-colors">
                                    Αποσύνδεση
                                </h3>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- Login Form for non-authenticated schools -->
        <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full space-y-8">
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <div class="text-center mb-8">
                        <img src="{{ asset('favicon/android-chrome-512x512.png') }}" alt="Logo" class="mx-auto h-20 w-20 mb-4">
                        <h2 class="text-3xl font-bold text-gray-900">Σύνδεση Σχολείου</h2>
                        <p class="mt-2 text-gray-600">Εισάγετε τα στοιχεία σας για πρόσβαση</p>
                    </div>
                    
                    @if(session('error'))
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('school.login') }}" class="space-y-6">
                        @csrf
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">7ψήφιος Κωδικός Σχολείου</label>
                            <input type="text" 
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('username') border-red-500 @enderror" 
                                   id="username" 
                                   name="username" 
                                   required
                                   placeholder="Εισάγετε τον κωδικό σας">
                            @error('username')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Κωδικός Πρόσβασης</label>
                            <input type="password" 
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('password') border-red-500 @enderror" 
                                   id="password" 
                                   name="password" 
                                   required
                                   placeholder="Εισάγετε τον κωδικό πρόσβασης">
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="remember" 
                                   name="remember" 
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-sm text-gray-700">
                                Μόνιμη Σύνδεση
                            </label>
                        </div>
                        
                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-indigo-500 to-purple-600 text-white py-3 px-4 rounded-xl hover:from-indigo-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-200 font-semibold">
                            <i class="fas fa-sign-in-alt mr-2"></i>Σύνδεση
                        </button>
                    </form>
                    
                    <div class="text-center mt-6">
                        <p class="text-sm text-gray-500">
                            Αν έχετε ξεχάσει τον κωδικό σας, επικοινωνήστε με την υποστήριξη.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endauth

    </body>
</x-layout_school>