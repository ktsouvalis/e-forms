<x-layout>
    <div class="container">
        <div class="row">
            <form class="col-md py-2" action="{{url('/com_change_active_month')}}" method="post">
                @csrf
                <button type="submit" class="btn btn-warning"><div class="fa-brands fa-laravel"></div>  artisan change-active-month</button>
            </form>
            <form class="col-md py-2" action="{{url('/com_change_microapp_accept_status')}}" method="post">
                @csrf
                <button type="submit" class="btn btn-warning"><div class="fa-brands fa-laravel"></div>  artisan microapps:accept_not</button>
            </form>
            <form class="col-md py-2" action="{{url('/com_edit_super_admins')}}" method="post">
                @csrf
                <div class="input-group my-2">
                    <input name="username" type="text" class="form-control" placeholder="username" aria-label="username" aria-describedby="basic-addon2" required>
                </div>
                <button type="submit" class="btn btn-warning"><div class="fa-brands fa-laravel"></div>  artisan super a_username</button>
            </form>
            @if(!$maintenanceMode)
            <form class="col-md py-2" action="{{url('/app_down')}}" method="post">
                @csrf
                <div class="input-group my-2">
                    <input name="secret" type="text" class="form-control" placeholder="secret" aria-label="secret" aria-describedby="basic-addon3" required>
                </div>
                <button type="submit" class="btn btn-warning"><div class="fa-brands fa-laravel"></div>  artisan down</button>
            </form>
            @else
            <form class="col-md py-2" action="{{url('/app_up')}}" method="post">
                @csrf
                <button type="submit" class="btn btn-warning"><div class="fa-brands fa-laravel"></div>  artisan up</button>
            </form>
            @endif
            <form class="col-md py-2" action="{{url('/com_eDirecorate_update')}}" method="post">
                @csrf
                <button type="submit" class="btn btn-warning"><div class="fa-brands fa-laravel"></div>  artisan update-e-directorate</button>
            </form>
            @php
                $dir_info = DB::table('directorate_info')->find(1);
                if($dir_info){
                    $dname=$dir_info->name;
                    $dcode=$dir_info->code;
                }
            @endphp
            <form class="col-md py-2" action="{{url('/com_directorate_name_update')}}" method="post">
                @csrf
                <div class="input-group my-2">
                    <input name="dir_name" type="text" class="form-control" placeholder="π.χ. ΔΙΕΥΘΥΝΣΗ Π.Ε. ΑΧΑΪΑΣ"  value="@isset($dname) {{$dname}}@endisset" required>
                </div>
                <button type="submit" class="btn btn-warning"><div class="fa-brands fa-laravel"></div>  artisan app:udn</button>
            </form>
            <form class="col-md py-2" action="{{url('/com_directorate_code_update')}}" method="post">
                @csrf
                <div class="input-group my-2">
                    <input name="dir_code" type="text" class="form-control" placeholder="π.χ. 9906101" value="@isset($dcode) {{$dcode}}@endisset" required>
                </div>
                <button type="submit" class="btn btn-warning"><div class="fa-brands fa-laravel"></div>  artisan app:udc</button>
            </form>
        </div>
    </div>
</x-layout>