<x-layout>
    @php
        $school = Auth::guard('school')->user(); //check which school is logged in
        $school_code = $school->code;
        
      
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
    
                            <div class="form-group row">
                                <label for="category" class="col-md-4 col-form-label text-md-right">{{ __('Κατηγορία') }}</label>
    
                                <div class="col-md-6">
                                    <input id="category" type="text" class="form-control @error('category') is-invalid @enderror" name="category" value="{{ old('category') }}" required autocomplete="category" autofocus>
    
                                    @error('category')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
    
                            <div class="form-group row">
                                <label for="description" class="col-md-4 col-form-label text-md-right">{{ __('Τίτλος Δράσης') }}</label>
    
                                <div class="col-md-6">
                                    <textarea id="description" class="form-control @error('Τίτλος') is-invalid @enderror" name="description" required autocomplete="description">{{ old('title') }}</textarea>
    
                                    @error('description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
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