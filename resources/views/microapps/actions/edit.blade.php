{{-- filepath: c:\Users\stefanopoulos\Desktop\e-forms\resources\views\microapps\actiontypes\edit.blade.php --}}
<x-layout>
    @php
        $action = $action ?? null;
        $action_types = App\Models\microapps\ActionType::get();  //get the action types
        //dd($action);
    @endphp

    <div class="container mt-5">
        <h3>Επεξεργασία Εκπαιδευτικής Δράσης</h3>
        <form action="{{ route('actions.update', $action->id) }}" method="post">
            @csrf
            @method('PUT')
            {{-- Category --}}
            <div class="form-group row">
                <label for="category" class="col-md-4 col-form-label text-md-right">{{ __('Κατηγορία') }}</label>

                <div class="col-md-6">
                    <select id="category" class="form-control @error('category') is-invalid @enderror" name="category" required>
                        <option value="">{{ __('Επιλέξτε Κατηγορία') }}</option>
                        @foreach($action_types as $type)
                            <option value="{{ $type->id }}" @if($type->id == $action->actiontype_id) selected @endif >{{ $type->description }}</option>
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
                    <textarea id="implementing_authority" class="form-control @error('implementing_authority') is-invalid @enderror" name="implementing_authority" required autocomplete="implementing_authority">{{ $action->implementing_authority }}</textarea>

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
                    <textarea id="title" class="form-control @error('title') is-invalid @enderror" name="title" required autocomplete="title">{{ $action->title }}</textarea>

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
                    <textarea id="teachers" class="form-control @error('teachers') is-invalid @enderror" name="teachers" required autocomplete="teachers">{{ $action->teachers }}</textarea>

                    @error('teachers')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <label for="number_of_teachers" class="col-md-4 col-form-label text-md-right">{{ __('Αριθμός Εκπαιδευτικών') }}</label>
                
                <div class="col-md-2">
                    <input id="number_of_teachers" type="number" min="1" class="form-control @error('number_of_teachers') is-invalid @enderror" name="number_of_teachers" value={{$action->number_of_teachers}} required>

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
                    <textarea id="records" class="form-control @error('records') is-invalid @enderror" name="records" autocomplete="records">{{ $action->records }}</textarea>
                    
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
                    <textarea id="comments" class="form-control @error('comments') is-invalid @enderror" name="comments" autocomplete="comments">{{ $action->comments }}</textarea>
                    
                    @error('comments')
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
            <a href="{{ route('actions.index') }}" class="btn btn-secondary">Ακύρωση</a>
        </form>
    </div>
</x-layout>