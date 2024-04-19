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
        <table class="m-2 align-middle table table-striped table-hover">
            <thead>
                <tr>
                    <th>Τύπος</th>
                    <th>Σύνοψη</th>
                    <th>Ημερομηνία</th>
                    <th>Διαβάστηκε</th>
                    <th>Διαγραφή</th>
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
                    <tr class="{{$color}}" id="notification-{{$notification->id}}">
                        <td>{{ $notification->type }}</td>
                        <td><a href="{{ route('notifications.show', $notification->id) }}">{{ $notification->data['summary'] }}</a></td>
                        <td>{{ $notification->created_at }}</td>
                        <td class="mark-{{$notification->id}}" style="text-align:center">
                        @if($notification->read_at == null)
                           <button class="btn btn-success bi bi-check2 mark-notification" data-notification-id="{{ $notification->id }}"></button>
                        @else
                            <button class="btn btn-secondary bi bi-check2" disabled></button>
                        @endif
                        </td>
                        <td style="text-align:center"><button class="btn btn-danger bi bi-trash delete-notification" data-notification-id="{{ $notification->id }}"></button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <form action="{{route("notifications.mark_all_as_read")}}" method="post">
            @csrf
            <button type="submit" class="m-2 btn btn-dark bi bi-check2"> Διαβάστηκαν όλες οι ειδοποιήσεις</button>
        </form>
    @endif
</x-layout>