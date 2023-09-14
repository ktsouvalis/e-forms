<x-layout>
    <div class="container p-3">
        <div class="hstack gap-3">
            <form action="{{url('/com_change_active_month')}}" method="post">
                @csrf
                <button type="submit" class="btn btn-warning"><div class="fa-brands fa-laravel"></div>  artisan change-active-month</button>
            </form>
            <form action="{{url('/com_change_microapp_accept_status')}}" method="post">
                @csrf
                <button type="submit" class="btn btn-warning"><div class="fa-brands fa-laravel"></div>  artisan microapps:accept_not</button>
            </form>
            <form action="{{url('/com_edit_super_admins')}}" method="post">
                @csrf
                <div class="input-group my-2">
                    <input name="username" type="text" class="form-control" placeholder="username" aria-label="username" aria-describedby="basic-addon2" required>
                </div>
                <button type="submit" class="btn btn-warning"><div class="fa-brands fa-laravel"></div>  artisan super a_username</button>
            </form>
            @if(!$maintenanceMode)
            <form action="{{url('/app_down')}}" method="post">
                @csrf
                <div class="input-group my-2">
                    <input name="secret" type="text" class="form-control" placeholder="secret" aria-label="secret" aria-describedby="basic-addon3" required>
                </div>
                <button type="submit" class="btn btn-warning"><div class="fa-brands fa-laravel"></div>  artisan down</button>
            </form>
            @else
            <form action="{{url('/app_up')}}" method="post">
                @csrf
                <button type="submit" class="btn btn-warning"><div class="fa-brands fa-laravel"></div>  artisan up</button>
            </form>
            @endif
        </div>
    </div>
</x-layout>