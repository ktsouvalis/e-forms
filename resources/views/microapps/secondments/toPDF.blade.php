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

        footer {
            position: fixed; 
            bottom: 0cm; 
            left: 0cm; 
            right: 0cm;
            height: 1cm;

            /** Extra personal styles **/
            background-color: #f5f5f5;
            color: #555;
            text-align: center;
            line-height: 0.8rem;
            font-size: 0.5rem;
        }
    </style>
  </head>
  @php
    $sec = App\Models\microapps\Secondment::find($secondment);
  @endphp
  <body>
    
    <div style="width: 100%; ">
      {{-- <div style="margin-right: -15px; margin-left: -15px;"> --}}
          <div style="display: flex; flex-wrap: wrap; justify-content: center;">
              {{-- <div style="flex: 0 0 33.333333%; max-width: 33.333333%; position: relative; width: 100%; padding-right: 15px; padding-left: 15px;"> --}}
                  <div style="flex: 1 1 auto; min-height: 1px; padding: 0; position: relative; word-wrap: break-word; background-color: #fff; background-clip: border-box; border: 1px solid rgba(0,0,0,.125); border-radius: .25rem;">
                      {{-- <h4 style="margin-bottom: .5rem; font-weight: 500; line-height: 1.2;">Αίτηση απόσπασης σε σχολική μονάδα εντός ΠΥΣΠΕ Αχαΐας</h4> --}}
                      <p style="margin-top: 0; margin-bottom: 0;">Σχ. Έτος 2024-25</p>
                      <div style="display: block; width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
                          <table style="width: 100%; border: 1px solid #0a58ca; margin-bottom: 1rem; color: #212529;">
                              <thead>
                                  <tr>
                                      <th colspan="4" style="padding: 0px margin: 1rem; vertical-align: top; color: #fff; background-color: #0DCAF0;">Αίτηση απόσπασης σε σχολική μονάδα εντός ΠΥΣΠΕ Αχαΐας</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  <tr>
                                      <td colspan="2" style="padding: .75rem; border-top: 1px solid #dee2e6; border-left: 1px solid #dee2e6; text-align: right; ">Ονοματεπώνυμο: </td>
                                      <td colspan="2" style="padding: .75rem; border-top: 1px solid #dee2e6; border-left: 1px solid #dee2e6; text-align: left;">Στεφανόπουλος Κωνσταντίνος</td>
                                    </tr>
                                    <tr>
                                      <td colspan="2" style="padding: .75rem; border-top: 1px solid #dee2e6; border-left: 1px solid #dee2e6; text-align: right; ">Κλάδος: </td>
                                      <td colspan="2" style="padding: .75rem; border-top: 1px solid #dee2e6; border-left: 1px solid #dee2e6; text-align: left;">ΠΕ70</td>
                                  </tr>
                                  <tr>
                                    <td colspan="2" style="padding: .75rem; border-top: 1px solid #dee2e6; border-left: 1px solid #dee2e6;  text-align: right; ">Οργανική: </td>
                                      <td colspan="2" style="padding: .75rem; border-top: 1px solid #dee2e6; border-left: 1px solid #dee2e6; text-align: left;">5ο ΔΗΜΟΤΙΚΟ ΣΧΟΛΕΙΟ ΑΙΓΙΟΥ "ΓΕΩΡΓΙΑ ΠΑΝΟΥΤΣΟΠΟΥΛΟΥ-ΠΑΠΑΣΗΜΑΚΟΠΟΥΛΟΥ"</td>
                                  </tr>
                                  <tr>
                                      <td colspan="2" style="padding: .75rem; border-top: 1px solid #dee2e6; border-left: 1px solid #dee2e6; text-align: right; ">ΑΜ: </td>
                                      <td colspan="2" style="padding: .75rem; border-top: 1px solid #dee2e6; border-left: 1px solid #dee2e6; text-align: left;">625428</td>
                                </tr>
                              </tbody>
                          </table>
                          <table style="width: 100%; color: #212529;">
                            <tbody>
                                <tr>
                                    <td style="text-align: left;" >Παρακαλώ να με αποσπάσετε σε ένα από τα ακόλουθα σχολεία κατά σειρά προτίμησης:</td>
                                </tr>    
                            </tbody>
                          </table>
                          <div>
                            @php
                        
                                $schools_array = [];
                                $selectedSchools = [];
                                $selectedSchools = json_decode($selectionOrder); 
                                foreach($selectedSchools as $schoolCode){
                                    $school = App\Models\School::where('code', $schoolCode)->first();
                                    array_push($schools_array, $school->name);
                                }
                                $c=1;
                            @endphp
                            <div style="display: flex; flex-wrap: wrap;">
                                <div style="flex: 0 0 100%;">
                                    <table style="width: 100%; border-left: 1px solid #dee2e6; color: #212529;">
                                        @for($i=0; $i<count($schools_array); $i++)
                                            <tr>
                                                <td>{{$c++}}.</td>
                                                <td style="width: 50%;">{{str_replace("ΔΗΜΟΤΙΚΟ ΣΧΟΛΕΙΟ", "Δ.Σ.", $schools_array[$i])}}</td>
                                                {{-- <td>{{$c + 20}}.</td>
                                                <td style="width: 50%;">{{str_replace("ΔΗΜΟΤΙΚΟ ΣΧΟΛΕΙΟ", "Δ.Σ.", $schools_array[$i + 20])}}</td> --}}
                                            </tr>
                                        @endfor
                                    </table>
                                </div>
                            </div>   
                      </div>
                  </div>
              {{-- </div> --}}
          </div>
      </div>
    </div>
    <footer>
        Αυτόματη δημιουργία εγγράφου από Ηλεκτρονικές Φόρμες της Δ/νσης Π.Ε. Αχαΐας
    </footer>
  </body>
</html>