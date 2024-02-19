edit one test

<layout>
<form action="{{url("/tests/$test->id")}}" method="post">
    @csrf
    @method('PUT')
    <label for="name">Name</label>
    <input type="text" class="form-control" id="name" name="name" value="{{$test->name}}" required>
    <button type="submit">ok</button>
</form>
</layout>