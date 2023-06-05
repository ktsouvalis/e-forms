<x-layout_forms>
   <script>function addRawToUrl(url, id) {
      document.location.href = url;
   }</script>
@php
   if(!isset($form)){
      $form = new App\Models\Form;
   }
@endphp
   <div class="row">
   <div class="container py-5">
      <div class="container px-5">
          <nav class="navbar navbar-light bg-light">
              <form action="{{url("/form_edit2_stakeholders")}}" method="post" class="container-fluid">
                  @csrf
                  {{-- <div class="input-group">
                      <span class="input-group-text w-25"></span>
                      <span class="input-group-text w-75"><strong>Επεξεργασία Χρήστη</strong></span>
                  </div> --}}
                  Επεξεργασία Φόρμας
                  <div class="input-group">
                      <span class="input-group-text w-25" id="basic-addon2">Ονομασία Φόρμας</span>
                      <input name="name" type="text" class="form-control" placeholder="γράψτε ένα πολύ σύντομο τίτλο όπως θα εμφανίζεται στα μενού π.χ. Έντυπα Απόλυσης Αναπληρωτών" aria-label="name" aria-describedby="basic-addon2" required value="{{$form->name}}"><br>
                  </div>
                  <div class="input-group">
                     <span class="input-group-text w-25" id="basic-addon2">Περιγραφή Φόρμας</span>
                     <input name="name" type="text" class="form-control" placeholder="π.χ. Εξατομικευμένη παράδοση εντύπων απόλυσης Αναπληρωτών Εκπαιδευτικών σχ. έτους 2022-23" aria-label="name" aria-describedby="basic-addon2" required value="{{$form->name}}"><br>
                 </div>
                  <div class="input-group">
                      <span class="input-group-text w-25" id="basic-addon3">Χρώμα στο μενού</span>
                      <input name="color" type="text" class="form-control" placeholder="χρώμα" aria-label="color" aria-describedby="basic-addon3" required value="{{$form->color}}"><br>
                  </div>
                  <div class="input-group">
                      <span class="input-group-text w-25" id="basic-addon4">Ενεργή</span>
                      <input name="active" type="checkbox" aria-label="active" aria-describedby="basic-addon4" ><br>
                  </div>
                  <div class="input-group">
                     <span class="input-group-text w-25" id="basic-addon5">Ορατή στα Σχολεία/Εκπαιδευτικούς</span>
                     <input name="visible" type="checkbox" aria-label="visible" aria-describedby="basic-addon5" ><br>
                 </div>
                 <div class="input-group">
                  <span class="input-group-text w-25" id="basic-addon6">Δέχεται Υποβολές</span>
                  <input name="accepts" type="checkbox" aria-label="accepts" aria-describedby="basic-addon6" ><br>
                 </div>
                 <div class="input-group">
                  <span class="input-group-text w-25" id="basic-addon6">Δέχεται Υποβολές απο:</span>
                  <input name="opens_at" type="date" aria-label="accepts" aria-describedby="basic-addon6" ><br>
                 </div>
                 <div class="input-group">
                  <span class="input-group-text w-25" id="basic-addon6">Δέχεται Υποβολές έως:</span>
                  <input name="closes_at" type="date" aria-label="accepts" aria-describedby="basic-addon6" ><br>
                 </div>
                 
          </nav>
          @isset($dberror)
              <div class="alert alert-danger" role="alert">{{$dberror}}</div>
          @endisset
      </div>
  </div>    
</div>
@php
   $elements = App\Models\FormElements::All();
@endphp
<div class="row">
    <div class="col-sm-1 col-md-6 p-5">  <label for="question1" >Ερώτηση/Αρχείο: </label>
      <select name="question1" id="question1" onchange="addRawToUrl({{route('getFormElementAttributes', 1)}});">
         <option value="0"></option>
         @php foreach ($elements as $element) { @endphp
               <option value='{{$element->id}}'>{{$element->type}}</option>
         @php  } @endphp 
      </select>
    </div>
    <div class="col-sm-1 col-md-6 p-5">  <label for="question2" >Ερώτηση/Αρχείο: </label>
      <select name="question2" id="question2">
         <option value="0"></option>
         @php foreach ($elements as $element) { @endphp
               <option value='{{$element->id}}'>{{$element->type}}</option>
         @php  } @endphp 
         <p id="questionp1"></p>
      </select>
    </div>
 </div>
 <div class="row">
    <div class="col-sm-1 col-md-6 p-5">  Question 3 </div>
    <div class="col-sm-1 col-md-6 p-5"> Question 4</div>
 </div>
 <div class="row">
    <div class="col-sm-1 col-md-6 p-5">  Question 5 </div>
    <div class="col-sm-1 col-md-6 p-5"> Question 6</div>
 </div>
 <div class="row">
    <div class="col-sm-1 col-md-6 p-5">  Question 7 </div>
    <div class="col-sm-1 col-md-6 p-5"> Question 8</div>
 </div>
 <div class="row">
    <div class="col-sm-1 col-md-6 p-5">  Question 9 </div>
    <div class="col-sm-1 col-md-6 p-5"> Question 10</div>
 </div>
 <input type="hidden" name="form_id" value="{{$form->id}}">
 <div class="input-group">
     <span class="w-25"></span>
     <button type="submit" class="btn btn-primary bi bi-save m-2"> Αποθήκευση και συνέχεια</button>
     <a href="{{url("/forms")}}" class="btn btn-outline-secondary bi bi-arrow-counterclockwise m-2"> Ακύρωσην</a>
 
</form>
</x-layout_forms>
