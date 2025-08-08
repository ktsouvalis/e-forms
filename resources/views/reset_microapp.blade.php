<x-layout>

@push('title') 
    <title>Reset-{{ $microapp->name }}</title>
@endpush

<div class="col">
    <p class="h4">{{ $microapp->name }} - Αρχικοποίηση</p>
    <p class="text-muted">(Εδώ μπορείτε να αρχικοποιήσετε τη μικροεφαρμογή)</p>
</div>

<table>    
    <tr>
        <td ><strong>Βήμα 1:</strong> Λήψη Backup όλων των αρχείων της μικροεφαρμογής</td>
        <td>
            <form action="{{ route('download_files',  ['microapp' => $microapp->id])}}" method="GET">
                @csrf
                <button type="submit">Λήψη Αρχείων</button>
            </form>
        </td>
    </tr>
    <tr>
        <td ><strong>Βήμα 2:</strong> Λήψη excel αρχείου όλων των εγγραφών της ΒΔ που αφορουύν τη μικροεφαρμογή</td>
        <td>
            <form action="{{ route('download_excel',  ['microapp' => $microapp->id])}}" method="GET">
                <button type="submit">Λήψη Excel</button>
            </form>
        </td>
    </tr>
    <tr>
        <td ><strong>Βήμα 3:</strong> Διαγραφή αρχείων από τον server</td>
        <td>
            <form action="{{ route('delete_files',  ['microapp' => $microapp->id])}}" method="GET">
                @csrf
                <button type="submit">Διαγραφή Αρχείων</button>
            </form>
        </td>
    </tr>
    <tr>
        <td ><strong>Βήμα 4:</strong> Αρχικοποίηση Βάσης Δεδομένων</td>
        <td>
            <form action="{{ route('reset_db',  ['microapp' => $microapp->id])}}" method="GET">
                @csrf
                <button type="submit">Αρχικοποίηση ΒΔ</button>
            </form>
        </td>
    </tr>

</table>

</x-layout>