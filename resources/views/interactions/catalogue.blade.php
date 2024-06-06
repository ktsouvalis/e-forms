<x-layout>
    @push('title')
        <title>{{$name}}</title>
    @endpush
    @push('scripts')
    <script>
        var resolveInteractionUrl = '{{ route("interactions.resolve", ["interaction" =>"mpla"]) }}';
    </script>
    <script>
        $(document).ready(function() {
            $('.resolve-checkbox').change(function() {
                var isResolved = $(this).is(':checked') ? 1 : 0;
                var interactionId = $(this).data('id');

                $.ajax({
                    url: resolveInteractionUrl.replace("mpla", interactionId),
                    method: 'POST',
                    data: {
                        resolved: isResolved,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        // Handle success here
                        console.log(response);
                        if (response.resolved==1) {
                            $('#row_' + interactionId).addClass('table-success');
                        } else {
                            $('#row_' + interactionId).removeClass('table-success');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        // Handle error here
                        console.log(textStatus, errorThrown);
                    }
                });
            });
        });
    </script>
    @endpush
    <div class="container">
        <div class="table-responsive py-2">
            <table id="dataTable" class="display table table-sm table-striped table-hover">
            <thead>
                <tr>
                    <th id="search">Αριθμός Πρωτοκόλλου</th>
                    <th id="search">Ημερομηνία</th>
                    <th id="search">Ενδιαφερόμενος</th>
                    <th id="search">Κείμενο</th>
                    <th>Αρχεία</th>
                    <th id="search">Διεκπεραίωση</th>
                </tr>
            </thead>
            @foreach($interactions as $interaction)
                <tr id="row_{{$interaction->id}}">
                    <td>{{ $interaction->protocol_number }}</td>
                    <td>{{ $interaction->created_at }}</td>
                    @if($interaction->stakeholder_type == 'App\Models\School')
                        <td>{{ $interaction->stakeholder->name }}</td>
                    @else
                        <td>{{ $interaction->stakeholder->surname }} {{ $interaction->stakeholder->name }}</td>
                    @endif
                    <td>{!!html_entity_decode($interaction->text) !!}</td>
                    @php
                        if($interaction->files != null)
                            $files = json_decode($interaction->files);
                    // dd($files);
                    @endphp
                    <td>
                    @foreach($files as $file)
                        @php
                            $url = route('interactions.download_file', ['interaction' => $interaction->id, 'filename' => $file->filename]);
                        @endphp
                        <form action="{{$url}}" method="get">
                            @csrf
                            <button type="submit" class="btn btn-secondary bi bi-download"> {{$file->filename}}</button>
                        </form>
                    @endforeach
                    </td>
                    <td>
                        <input type="checkbox" class="resolve-checkbox" data-id="{{ $interaction->id }}" {{ $interaction->resolved ? 'checked' : '' }}>
                    </td>
                </tr>
            @endforeach
</x-layout>