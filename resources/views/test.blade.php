<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <strong>{{$teacher->surnname}} {{$teacher->name}}</strong><br>
    Οργανική Τοποθέτηση: {{$teacher->organiki->name}} <br> 
    Υπηρέτηση: {{$teacher->ypiretisi->name}} <br> <br>
    <strong>Υπηρετούντες εκπαιδευτικοί στο {{$teacher->organiki->name}}</strong><br>
    @foreach($teacher->organiki->ypiretisis as $school_teacher)
        Επίθετο: {{$school_teacher->surnname}} <br>
        Όνομα: {{$school_teacher->name}} <br>
        Οργανική: {{$school_teacher->organiki->name}} <br><br>
    @endforeach
</body>
</html>

