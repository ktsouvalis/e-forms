<x-layout>
welcome to tests

@foreach($tests as $test)
    <li>{{$test->name}}</li>
    <form action="{{url("/tests/$test->id")}}" method="post">
        @csrf
        @method('DELETE')
        <button type="submit">delete</button>
    </form>
@endforeach
</x-layout>