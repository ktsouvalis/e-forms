@php
    $maintenanceMode = app()->isDownForMaintenance();
    $dir_info = DB::table('directorate_info')->find(1);
    if($dir_info){
        $dname=$dir_info->name;
        $dcode=$dir_info->code;
    }
@endphp
<div class="modal fade" id="commandsModal" tabindex="-1" role="dialog" aria-labelledby="commandsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commandsModalLabel">Εντολές Διαχείρισης</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                <form class="col-md py-2" action="{{url('/update_app')}}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-success bi bi-arrow-repeat"> Ενημέρωση Εφαρμογής</button>
                </form>
                <form class="col-md py-2" action="{{url('/db_backup')}}" method="post" data-export>
                    @csrf
                    <button type="submit" class="btn btn-success bi bi-database-down"> Database Backup</button>
                </form>
                <form class="col-md py-2" method="GET" action="{{ url('/get_logs') }}">
                    <div class="input-group my-2">
                        <input type="date" id="date" name="date" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary bi bi-journal-text"> Download Logs</button>
                </form>
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
                
                <form class="col-md py-2" action="{{url('/com_directorate_name_update')}}" method="post">
                    @csrf
                    <label style="font-size:12px">@isset($dname){{$dname}}@endisset</label>
                    <div class="input-group my-2">
                        <input name="dir_name" type="text" class="form-control" placeholder="π.χ. ΔΙΕΥΘΥΝΣΗ Π.Ε. ΑΧΑΪΑΣ" required>
                    </div>
                    <button type="submit" class="btn btn-warning"><div class="fa-brands fa-laravel"></div>  artisan app:udn</button>
                </form>
                <form class="col-md py-2" action="{{url('/com_directorate_code_update')}}" method="post">
                    @csrf
                    <label style="font-size:14px">@isset($dcode){{$dcode}}@endisset</label>
                    <div class="input-group my-2">
                        <input name="dir_code" type="text" class="form-control" placeholder="π.χ. 9906101" required>
                    </div>
                    <button type="submit" class="btn btn-warning"><div class="fa-brands fa-laravel"></div>  artisan app:udc</button>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>
