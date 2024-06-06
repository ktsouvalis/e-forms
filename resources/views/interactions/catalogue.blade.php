<x-layout>
    <div class="container">
        <div class="table-responsive py-2">
            <table id="dataTable" class="display table table-sm table-striped table-hover">
            <thead>
                <tr>
                    <th id="search">Αριθμός Πρωτοκόλλου</th>
                    <th id="search">Ημερομηνία</th>
                    <th id="search">Ενδιαφερόμενος</th>
                    <th id="search">Κείμενο</th>
                    <th id="search">Διεκπεραίωση</th>
                    
                </tr>
            </thead>
            @foreach($interactions as $interaction)
                <tr>
                    <td>{{ $interaction->protocol_number }}</td>
                    <td>{{ $interaction->created_at }}</td>
                    @if($interaction->stakeholder_type == 'App\Models\School')
                        <td>{{ $interaction->stakeholder->name }}</td>
                    @else
                        <td>{{ $interaction->stakeholder->surname }} {{ $interaction->stakeholder->name }}</td>
                    @endif
                    <td>{!!html_entity_decode($interaction->text) !!}</td>
                    <td>{{ $interaction->resolved }}</td>
                </tr>
            @endforeach
</x-layout>