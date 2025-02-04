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
<form action="{{url("consultants/$consultant->id")}}" method="post">
    @method('PUT')
    @csrf
    <div class="mb-3">
        <label for="afm" class="form-label">ΑΦΜ</label>
        <input type="text" class="form-control" id="afm" name="afm" value="{{$consultant->afm}}">
        <label for="am" class="form-label">ΑΜ</label>
        <input type="text" class="form-control" id="am" name="am" value="{{$consultant->am}}">
        <label for="mail" class="form-label">email</label>
        <input type="text" class="form-control" id="mail" name="mail" value="{{$consultant->mail}}">
        <label for="surname" class="form-label">Επώνυμο</label>
        <input type="text" class="form-control" id="surname" name="surname" value="{{$consultant->surname}}">
        <label for="name" class="form-label">Όνομα</label>
        <input type="text" class="form-control" id="name" name="name" value="{{$consultant->name}}">
        <label for="klados" class="form-label">Κλάδος</label>
        <input type="text" class="form-control" id="klados" name="klados" value="{{$consultant->klados}}">
        <label for="isSupervisor" class="form-label">Επόπτης Ποιότητας</label>
        <input type="checkbox" class="form-check-input" id="isSupervisor" name="isSupervisor" @if($consultant->isSupervisor()) checked @endif>
    </div>
    <div class="mb-3">
        <button type="submit" class="btn btn-primary">Αποθήκευση</button>
    </div>
</form>
@php
    $isInSchRegions = DB::table('schregions')->where('consultant_id', $consultant->id)->first();

@endphp
{{-- Display DELETE button only for Consultants NOT in schregions Table --}}
@if(!$isInSchRegions)
    <form action="{{url("consultants/$consultant->id")}}" method="post">
        @method('DELETE')
        @csrf
        <div class="mb-3">
            <button type="submit" class="btn btn-danger">Διαγραφή</button>
        </div>
    </form>
@endif

</x-layout>