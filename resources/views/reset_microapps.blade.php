<x-layout>
@php
    $active_microapps = App\Models\Microapp::where('active', 1)->whereNotIn('id', [1])->get();
@endphp
@push('title') 
    <title>Αρχικοποίηση Μικροεφαρμογών</title>
@endpush

<div class="col">
    <p class="h4">Αρχικοποίηση Μικροεφαρμογών</p>
    <p class="text-muted">(Εδώ μπορείτε να αρχικοποιήσετε τις μικροεφαρμογές επιλέγοντας το κουμπί "Διαδικασία Αρχικοποίησης")</p>
</div>

<table>    
@foreach($active_microapps as $microapp)
    <tr>
        <td >{{$microapp->id}}</td> 
        <td >{{$microapp->name}}</td>
        <td>
            <form action="{{ route('reset.view', $microapp) }}" method="GET">
                @csrf
                <button type="submit">Διαδικασία Αρχικοποίησης</button>
            </form>
        </td>

    </tr>
@endforeach
</table>
17/03/2026 - Ολοκληρώθηκε και λειτουργεί η αρχικοποίηση "Εγγραφές και προγραμματισμός"
29/05/2026 - Λειτουργούν Κτιριολογικά Προβλήματα, Αποσπάσεις εντός ΠΥΣΠΕ.
</x-layout>
    
