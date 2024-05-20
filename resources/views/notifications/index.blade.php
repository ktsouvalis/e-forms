<x-layout>
    
    @push('scripts')
        <script>
            var markNotificationAsReadUrl = '{{ route("notifications.mark_as_read", ["notification" =>"mpla"]) }}';
            var deleteNotificationUrl = '{{ route("notifications.destroy", ["notification" =>"mpla"]) }}';
        </script>
        <script src="{{asset('mark_notification_as_read.js')}}"></script>
        <script src="{{asset('delete_notification.js')}}"></script>
    @endpush
    @push('title')
        <title>Ειδοποιήσεις</title>
    @endpush
    <h4>Ειδοποιήσεις</h4>
    @if($notifications->count() == 0)
        <div class="alert alert-info">Δεν υπάρχουν ειδοποιήσεις</div>
    @else
        @if($notifications->whereNull('read_at')->count() > 1)
            <form action="{{route("notifications.mark_all_as_read")}}" method="post">
                @csrf
                <button type="submit" class="m-2 btn btn-primary"><i class="bi bi-check2-circle"></i> Σήμανση όλων ως αναγνωσμένα</button>
            </form>
        @endif
        @php
            $user = Auth::user();
        @endphp
        <form action="{{route("notifications.delete_all", $user) }}" method="post">
            @csrf
            <button type="submit" class="m-2 btn btn-danger"><i class="bi bi-x-circle"></i> Διαγραφή Όλων</button>
        </form>
        <table id="dataTable" class="m-2 align-middle table" style="text-align:center">
            <thead >
                <tr>
                    <th id="">Κατάσταση</th>
                    <th id="">Σύνοψη</th>
                    <th id="">Ημερομηνία</th>
                    <th id="">Διαγραφή</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($notifications as $notification)
                @php
                    if($notification->read_at == null){
                        $color = 'table-secondary';
                    }
                    else{
                        $color = '';
                    }
                @endphp
                    <tr class="{{$color}}" id="notification-{{$notification->id}}">
                        <td class="mark-{{$notification->id}}" style="text-align:center">
                        @if($notification->read_at == null)
                            <i id="icon{{$notification->id}}" class="text-primary fa-regular fa-envelope" data-toggle="tooltip" title="Μη Αναγνωσμένο"></i>
                        @else
                            <i class="text-secondary fa-regular fa-envelope-open" data-toggle="tooltip" title="Αναγνωσμένο"></i>
                        @endif
                        </td>
                        <td><a href="{{ route('notifications.show', $notification->id) }}">{{ $notification->data['summary'] }}</a></td>
                        <td>{{ $notification->created_at }}</td>
                        
                        <td id="actions{{$notification->id}}" style="text-align:center">@if($notification->read_at == null)<button id="mark{{$notification->id}}" class="btn btn-primary bi bi-check2-circle mark-notification" data-toggle="tooltip" title="Σήμανση ως Αναγνωσμένο" data-notification-id="{{ $notification->id }}"></button>@endif <button class="btn btn-outline-danger bi bi-trash delete-notification" data-notification-id="{{ $notification->id }}" data-toggle="tooltip" title="Διαγραφή"></button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
    @endif
</x-layout>