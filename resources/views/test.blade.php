<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    {{-- <strong>{{$teacher->surnname}} {{$teacher->name}}</strong><br>
    Οργανική Τοποθέτηση: {{$teacher->organiki->name}} <br> 
    Υπηρέτηση: {{$teacher->ypiretisi->name}} <br> <br>
    <strong>Υπηρετούντες εκπαιδευτικοί στο {{$teacher->organiki->name}}</strong><br>
    @foreach($teacher->organiki->ypiretisis as $school_teacher)
        Επίθετο: {{$school_teacher->surnname}} <br>
        Όνομα: {{$school_teacher->name}} <br>
        Οργανική: {{$school_teacher->organiki->name}} <br><br>
    @endforeach --}}

    {{-- @php
        $user = Auth::user();
        foreach($user->operations as $one_operation){
            // echo $one_operation->operation->url=='/teachers' and $one_operation->can_edit; exit;
            if($one_operation->operation->url=='/teachers' and $one_operation->can_edit){
                return true;
            } 
        }
        return false;
        ->microapps->where('microapp_id', $microapp->id)->first()->can_edit
    @endphp --}}

    {{Auth::user()->microapps->where('microapp_id', 4)->first()->can_edit}}
</body>
</html>

