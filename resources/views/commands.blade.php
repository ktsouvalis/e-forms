<x-layout>
    <div class="container">
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
                <button type="submit" class="btn btn-warning"><div class="fa-brands fa-laravel"></div>  artisan super: a_username</button>
            </form>
        </div>
    </div>
</x-layout>