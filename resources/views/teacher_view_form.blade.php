<x-layout_teacher>

@php

echo $form->name;
$elements=array();
$elements = json_decode($form->elements);
@endphp

<div class="row">
<div class="col-sm-1 col-md-6 p-5">  @php echo App\CustomClasses\HtmlGenerator::href("../storage/app/files/seperate_files1/", "kostas_file.txt", "MyFile.txt"); @endphp </div>
<div class="col-sm-1 col-md-6 p-5"><a href="../storage/app/files/seperate_files1/kostas_file.txt" download>Αρχειο </a></div>
</div>
   

</x-layout_teacher>