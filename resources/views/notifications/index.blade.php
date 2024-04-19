<x-layout>
    @push('title')
        <title>Ειδοποιήσεις</title>
    @endpush
    <h4>Ειδοποιήσεις</h4>
    @if($notifications->count() == 0)
        <div class="alert alert-info">Δεν υπάρχουν ειδοποιήσεις</div>
    @else
        <table class="m-2 align-middle table table-striped table-hover">
            <thead>
                <tr>
                    <th>Τύπος</th>
                    <th>Σύνοψη</th>
                    <th>Ημερομηνία</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($notifications as $notification)
                @php
                    if($notification->read_at == null){
                        $color = 'table-warning';
                    }
                    else{
                        $color = '';
                    }
                @endphp
                    <tr class="{{$color}}">
                        <td>{{ $notification->type }}</td>
                        <td><a href="{{ route('notifications.show', $notification->id) }}">{{ $notification->data['summary'] }}</a></td>
                        <td>{{ $notification->created_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</x-layout>