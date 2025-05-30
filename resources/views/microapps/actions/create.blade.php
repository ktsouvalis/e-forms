<x-layout>
    @php
        $school = Auth::guard('school')->user(); // Check which school is logged in
        $school_code = $school->code;
        $action_types = App\Models\microapps\ActionType::get();  // Get the action types
    @endphp
    
    @push('title')
        <title>Εκπαιδευτικές Δράσεις</title>
    @endpush
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Δημιουργία Εκπαιδευτικής Δράσης') }}</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('actions.store') }}">
                            @csrf
                            {{-- Category --}}
                            <div class="form-group row">
                                <label for="category" class="col-md-4 col-form-label text-md-right">{{ __('Κατηγορία') }}</label>
    
                                <div class="col-md-6">
                                    <select id="category" class="form-control @error('category') is-invalid @enderror" name="category" required>
                                        <option value="">{{ __('Επιλέξτε Κατηγορία') }}</option>
                                        @foreach($action_types as $type)
                                            <option value="{{ $type->id }}">{{ $type->description }}</option>
                                        @endforeach
                                    </select>
    
                                    @error('category')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            {{-- Implementing Authority --}}
                            <div class="form-group row">
                                <label for="implementing_authority" class="col-md-4 col-form-label text-md-right">{{ __('Φορέας Υλοποίησης') }}</label>
                                <div class="col-md-6">
                                    <textarea id="implementing_authority" class="form-control @error('implementing_authority') is-invalid @enderror" name="implementing_authority" required autocomplete="implementing_authority">{{ old('implementing_authority') }}</textarea>

                                    @error('implementing_authority')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            {{-- Title --}}
                            <div class="form-group row">
                                <label for="title" class="col-md-4 col-form-label text-md-right">{{ __('Τίτλος Δράσης') }}</label>
                                <div class="col-md-6">
                                    <textarea id="title" class="form-control @error('title') is-invalid @enderror" name="title" required autocomplete="title">{{ old('title') }}</textarea>

                                    @error('title')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Number of Teachers & Teachers' Names--}}
                            <div class="form-group row">
                                <label for="teachers" class="col-md-2 col-form-label text-md-right">{{ __('Υπεύθυνοι Εκπαιδευτικοί') }}</label>
                                
                                <div class="col-md-4">
                                    <textarea id="teachers" class="form-control @error('teachers') is-invalid @enderror" name="teachers" required autocomplete="teachers">{{ old('teachers') }}</textarea>

                                    @error('teachers')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <label for="number_of_teachers" class="col-md-4 col-form-label text-md-right">{{ __('Αριθμός Εκπαιδευτικών') }}</label>
                                
                                <div class="col-md-2">
                                    <input id="number_of_teachers" type="number" min="1" class="form-control @error('number_of_teachers') is-invalid @enderror" name="number_of_teachers" value="{{ old('number_of_teachers') }}" required>

                                    @error('number_of_teachers')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Records --}}
                            <div class="form-group row">
                                <label for="records" class="col-md-4 col-form-label text-md-right">{{ __('Αριθμός Πρωτ. Εγγράφου/Εγγράφων Έγκρισης') }}</label>
                                <div class="col-md-6">
                                    <textarea id="records" class="form-control @error('records') is-invalid @enderror" name="records" autocomplete="records">{{ old('records') }}</textarea>
                                    
                                    @error('records')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="comments" class="col-md-4 col-form-label text-md-right">{{ __('Σημειώσεις-Παρατηρήσεις') }}</label>
                                <div class="col-md-6">
                                    <textarea id="comments" class="form-control @error('comments') is-invalid @enderror" name="comments" autocomplete="comments" placeholder="π.χ. προσθήκη υπερσυνδέσμου από την Ιστοσελίδα του Σχολείου που αφορά τη Δράση">{{ old('comments') }}</textarea>
                                    
                                    @error('comments')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="alert alert-info w-100" role="alert">
                                    <strong>Σημείωση:</strong> Η δυνατότητα προσθήκης αρχείων (όπως έγγραφα ή φωτογραφίες) για τις Εκπαιδευτικές Δράσεις δεν είναι ακόμη διαθέσιμη.<br>
                                    Προς το παρόν, μπορείτε να προσθέσετε υπερσυνδέσμους σε σχετικά αρχεία που φιλοξενούνται στην Ιστοσελίδα του Σχολείου σας.<br>
                                    Η λειτουργία αυτή θα ενεργοποιηθεί το Σεπτέμβριο.
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Αποθήκευση') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>

