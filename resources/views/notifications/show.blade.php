<x-layout>


    <div class="container">
        <div class="card">
            <div class="card-header bi bi-bell bg-warning">
               Λεπτομέρειες
            </div>
            <div class="card-body">
                <p class="card-text">{!! $notification->data['message'] !!}</p>
                <p class="card-text"><small class="text-muted">{{ $notification->created_at }}</small></p>
            </div>
        </div>
    </div>
    <a href="{{route("notifications.index")}}" class="m-2 btn btn-outline-dark bi bi-arrow-return-left"> Επιστροφή στις Ειδοποιήσεις <a>
</x-layout>
