<x-layout>
    @push('title')
        <title>Σύμβουλοι Εκπαίδευσης</title>
    @endpush
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <body>    
    <p class="h4">Σύμβουλος Εκπαίδευσης</p>
    <form action="{{url("consultants")}}" method="post">
        @csrf
        <div class="mb-3">
            <label for="afm" class="form-label">ΑΦΜ</label>
            <input type="text" class="form-control" id="afm" name="afm">
            <label for="am" class="form-label">ΑΜ</label>
            <input type="text" class="form-control" id="am" name="am">
            <label for="mail" class="form-label">email</label>
            <input type="text" class="form-control" id="mail" name="mail">
            <label for="surname" class="form-label">Επώνυμο</label>
            <input type="text" class="form-control" id="surname" name="surname">
            <label for="name" class="form-label">Όνομα</label>
            <input type="text" class="form-control" id="name" name="name">
            <label for="klados" class="form-label">Κλάδος</label>
            <input type="text" class="form-control" id="klados" name="klados">
            <label for="isSupervisor" class="form-label">Επόπτης Ποιότητας</label>
            <input type="checkbox" class="form-check-input" id="isSupervisor" name="isSupervisor">
        </div>
        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Αποθήκευση</button>
        </div>
        
    </form>
    
    </x-layout>