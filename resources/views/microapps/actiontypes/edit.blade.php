{{-- filepath: c:\Users\stefanopoulos\Desktop\e-forms\resources\views\microapps\actiontypes\edit.blade.php --}}
<x-layout_consultant>
    @php
        $actionType = $actionType ?? null;
    @endphp

    <div class="container mt-5">
        <h3>Επεξεργασία Εκπαιδευτικής Δράσης</h3>
        <form action="{{ route('actiontypes.update', $actionType->id) }}" method="post">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="category" class="form-label">Γενική Κατηγορία</label>
                <input type="text" class="form-control" id="category" name="category" value="{{ old('category', $actionType->category) }}" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Περιγραφή</label>
                <textarea class="form-control" id="description" name="description" required>{{ old('description', $actionType->description) }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Αποθήκευση</button>
            <a href="{{ route('actiontypes.index') }}" class="btn btn-secondary">Ακύρωση</a>
        </form>
    </div>
</x-layout_consultant>