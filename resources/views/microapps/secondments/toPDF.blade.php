<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <style>
        @page {
            margin: 0cm 0cm;
        }

        header, footer {
            position: fixed; 
            left: 0cm; 
            right: 0cm;
            height: 50px;
        }
        header {
            top:0%;
        }

        footer {
            bottom: 0cm; 
            /** Extra personal styles **/
            background-color: #f5f5f5;
            color: #555;
            text-align: center;
            line-height: 0.8rem;
            font-size: 0.5rem;
        }
    </style>
  </head>
  <body>
    <header>
    </header>
        <table style="width: 95%; border: 1px solid #0a58ca; margin-bottom: 1rem; margin-top: 75px; color: #212529; margin-left: auto; margin-right: auto;">
            <thead>
                <tr>
                    <th colspan="4" style="padding: 0px margin: 1rem; vertical-align: top; color: #fff; background-color: #0DCAF0;">Αίτηση απόσπασης σε σχολική μονάδα εντός ΠΥΣΠΕ Αχαΐας</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2" style="padding: .75rem; border-top: 1px solid #dee2e6; border-left: 1px solid #dee2e6; text-align: right; ">Ονοματεπώνυμο: </td>
                    <td colspan="2" style="padding: .75rem; border-top: 1px solid #dee2e6; border-left: 1px solid #dee2e6; text-align: left;">{{$secondment->teacher->surname}} {{$secondment->teacher->name}}</td>
                </tr>
                <tr>
                    <td colspan="2" style="padding: .75rem; border-top: 1px solid #dee2e6; border-left: 1px solid #dee2e6; text-align: right; ">Κλάδος: </td>
                    <td colspan="2" style="padding: .75rem; border-top: 1px solid #dee2e6; border-left: 1px solid #dee2e6; text-align: left;">{{$secondment->teacher->klados}}</td>
                </tr>
                <tr>
                <td colspan="2" style="padding: .75rem; border-top: 1px solid #dee2e6; border-left: 1px solid #dee2e6;  text-align: right; ">Οργανική: </td>
                    <td colspan="2" style="padding: .75rem; border-top: 1px solid #dee2e6; border-left: 1px solid #dee2e6; text-align: left;">{{$secondment->teacher->organiki->name}}</td>
                </tr>
                <tr>
                    <td colspan="2" style="padding: .75rem; border-top: 1px solid #dee2e6; border-left: 1px solid #dee2e6; text-align: right; ">ΑΜ: </td>
                    <td colspan="2" style="padding: .75rem; border-top: 1px solid #dee2e6; border-left: 1px solid #dee2e6; text-align: left;">{{$secondment->teacher->am}}</td>
            </tr>
            </tbody>
        </table>
                        
        <table style="width: 95%; color: #212529; margin-left: auto; margin-right: auto;">
            <tbody>
                <tr>
                    <td style="text-align: left;" >Παρακαλώ να με αποσπάσετε σε ένα από τα ακόλουθα σχολεία κατά σειρά προτίμησης:</td>
                </tr>    
            </tbody>
        </table>

        @php
        //get selected schools in order and create an array with their names
            $schools_array = [];
            $selectedSchools = [];
            $selectedSchools = json_decode($selectionOrder); 
            foreach($selectedSchools as $schoolCode){
                $school = App\Models\School::where('code', $schoolCode)->first();
                array_push($schools_array, $school->name);
            }
        @endphp
        <table style='width: 93%; color: #212529; margin-left: auto; margin-right: auto;'>
            <tbody>
        @for($i=0; $i<190; $i++)
            @php
                if($i<=15)
                    $j=$i + 15;
                else
                    $j=$i + 20;
                $cLeft = $i + 1;
                $cRight = $j + 1; 
                if($i == count($schools_array)) 
                    break;
            @endphp

           
            @if($i == 30 || $i == 70 || $i == 110 || $i == 150)
                </tbody></table>
                <hr><div style='page-break-before: always;'></div><hr>
                <table style='width: 93%; color: #212529; margin-left: auto; margin-right: auto;'>
                <tbody>
            @endif
            @if(($i >= 0 && $i < 15) || ($i >= 30 && $i < 50) || ($i >= 70 && $i < 90) || ($i >= 110 && $i < 130) || ($i >= 150 && $i < 170))
                    <tr>
                        <td style="width: 5%;">{{isset($schools_array[$i])? $cLeft++ : ''}}</td>
                        <td style="width: 45%; word-wrap: break-word;">{{isset($schools_array[$i]) ?str_replace(["ΔΗΜΟΤΙΚΟ ΣΧΟΛΕΙΟ", "ΝΗΠΙΑΓΩΓΕΙΟ"],["Δ.Σ.", "ΝΗΠ."], $schools_array[$i]) : ''}}</td>
                        <td style="width: 5%;">{{isset($schools_array[$j]) ? $cRight: ''}}</td>
                        <td style="width: 45%; word-wrap: break-word;">{{isset($schools_array[$j]) ? str_replace(["ΔΗΜΟΤΙΚΟ ΣΧΟΛΕΙΟ", "ΝΗΠΙΑΓΩΓΕΙΟ"],["Δ.Σ.", "ΝΗΠ."], $schools_array[$j]) : ''}}</td>
                    </tr>
            @endif
        @endfor
    </tbody></table>

    <table style='width: 93%; color: #212529; margin-left: auto; margin-right: auto;'>
        <tbody>
            <tr>
                <td style="text-align: left;">Παρατήρηση - Επισήμανση: {{$secondment->preferences_comments}}.</td>
            </tr>
        </tbody>
    </table>
    <table style='width: 93%; color: #212529; margin-left: auto; margin-right: auto;'>
        <tbody>
            <tr>
                <td style="text-align: left;">{{isset($secondment->submit_date)?$secondment->submit_date->format('d/m/Y'):''}}</td>
            </tr>
        </tbody>
    </table>
    <div></div>
    <footer>
        Αυτόματη δημιουργία εγγράφου από Ηλεκτρονικές Φόρμες της Δ/νσης Π.Ε. Αχαΐας
    </footer>
  </body>
</html>