create form

# Path: resources/views/tests/create.blade.php
<layout>
<form action="{{url('/tests')}}" method="post">
    @csrf
    <label for="name">Name</label>
    <input type="text" class="form-control" id="name" name="name" required>
    <button type="submit">ok</button>
</form>
</layout>