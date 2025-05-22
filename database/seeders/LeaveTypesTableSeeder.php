<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LeaveTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leaveTypes = [
            [
                'description' => 'Αθλητική μετ\' αποδοχών (ν.2725/1999 άρ.34, παρ.23)',
                'eProtocolName' => null,
                'eProtocolId' => '14'
            ],
            [
                'description' => 'Αιμοδοτική',
                'eProtocolName' => 'ΕΙΔΙΚΗΣ ΑΔΕΙΑΣ ΑΙΜΟΔΟΣΙΑΣ',
                'eProtocolId' => '7'
            ],
            [
                'description' => 'Αιμοληψίας (σε εργάσιμη ημέρα)',
                'eProtocolName' => null,
                'eProtocolId' => '15'
            ],
            [
                'description' => 'Αναπλήρωσης με Αποδοχές (ν.3528/2007 άρ.50, παρ.7)',
                'eProtocolName' => null,
                'eProtocolId' => '16'
            ],
            [
                'description' => 'ΑΝΑΡΡΩΤΙΚΗ - λόγω Επαπειλούμενης Κύησης',
                'eProtocolName' => null,
                'eProtocolId' => '17'
            ],
            [
                'description' => 'ΑΝΑΡΡΩΤΙΚΗ - με Γνωμάτευση Α/βάθμιας Υγειονομικής Επιτροπής (όχι Επαπειλούμενης Κύησης)',
                'eProtocolName' => null,
                'eProtocolId' => '18'
            ],
            [
                'description' => 'ΑΝΑΡΡΩΤΙΚΗ - με Γνωμάτευση Ειδικής Υγειονομικής Επιτροπής',
                'eProtocolName' => null,
                'eProtocolId' => '19'
            ],
            [
                'description' => 'ΑΝΑΡΡΩΤΙΚΗ - με Γνωμάτευση Νοσοκομείου (ν.3528/2007 άρ.56, παρ.3)',
                'eProtocolName' => null,
                'eProtocolId' => '20'
            ],
            [
                'description' => 'ΑΝΑΡΡΩΤΙΚΗ - με Ιατρική Γνωμάτευση',
                'eProtocolName' => 'ΑΝΑΡΡΩΤΙΚΗ ΜΕ ΙΑΤΡΙΚΗ ΓΝΩΜΑΤΕΥΣΗ',
                'eProtocolId' => '1'
            ],
            [
                'description' => 'ΑΝΑΡΡΩΤΙΚΗ - με Υπεύθυνη Δήλωση',
                'eProtocolName' => 'ΑΝΑΡΡΩΤΙΚΗ ΜΕ ΥΠΕΥΘΥΝΗ ΔΗΛΩΣΗ',
                'eProtocolId' => '2'
            ],
            [
                'description' => 'Ανατροφής παιδιού (με πλήρεις αποδοχές)',
                'eProtocolName' => null,
                'eProtocolId' => '21'
            ],
            [
                'description' => 'Ανυπαίτιας αδυναµίας προσέλευσης λόγω δυσµενών καιρικών συνθηκών',
                'eProtocolName' => null,
                'eProtocolId' => '22'
            ],
            [
                'description' => 'Απουσία',
                'eProtocolName' => null,
                'eProtocolId' => '23'
            ],
            [
                'description' => 'Ασθένειας τέκνου',
                'eProtocolName' => 'ΛΟΓΩ ΑΣΘΕΝΕΙΑΣ ΤΕΚΝΟΥ',
                'eProtocolId' => '4'
            ],
            [
                'description' => 'Γάμου',
                'eProtocolName' => 'ΕΙΔΙΚΗΣ ΑΔΕΙΑΣ ΓΑΜΟΥ',
                'eProtocolId' => '6'
            ],
            [
                'description' => 'Για επιμορφωτικούς ή επιστημονικούς λόγους',
                'eProtocolName' => 'ΕΠΙΜΟΡΦΩΤΙΚΗ',
                'eProtocolId' => '11'
            ],
            [
                'description' => 'Για ετήσιο γυναικολογικό έλεγχο',
                'eProtocolName' => 'ΓΥΝΑΙΚΟΛΟΓΙΚΟΥ ΕΛΕΓΧΟΥ',
                'eProtocolId' => '12'
            ],
            [
                'description' => 'Για παράσταση διωκομένου',
                'eProtocolName' => null,
                'eProtocolId' => '24'
            ],
            [
                'description' => 'Για συμμετοχή σε δίκη',
                'eProtocolName' => 'ΕΙΔΙΚΗΣ ΑΔΕΙΑΣ ΣΥΜΜΕΤΟΧΗΣ ΣΕ ΔΙΚΗ',
                'eProtocolId' => '8'
            ],
            [
                'description' => null,
                'eProtocolName' => 'Ειδική για μετάβαση στην Ελλάδα (ν.4027/2011 άρ.16, παρ.4)',
                'eProtocolId' => '25'
            ],
            [
                'description' => 'Ειδική για Νόσημα',
                'eProtocolName' => 'ΕΙΔΙΚΗΣ ΑΔΕΙΑΣ ΝΟΣΗΜΑΤΟΣ',
                'eProtocolId' => '10'
            ],
            [
                'description' => 'Ειδική λόγω Αναπηρίας',
                'eProtocolName' => null,
                'eProtocolId' => '26'
            ],
            [
                'description' => 'Εξετάσεων',
                'eProtocolName' => 'ΕΙΔΙΚΗΣ ΑΔΕΙΑΣ ΕΞΕΤΑΣΕΩΝ',
                'eProtocolId' => '9'
            ],
            [
                'description' => 'Θανάτου (συζύγου ή συγγενούς έως και β βαθμού)',
                'eProtocolName' => 'ΕΙΔΙΚΗΣ ΑΔΕΙΑΣ ΠΕΝΘΟΥΣ',
                'eProtocolId' => '13'
            ],
            [
                'description' => 'Θανάτου τέκνου',
                'eProtocolName' => null,
                'eProtocolId' => '27'
            ],
            [
                'description' => 'Ιατρικώς Υποβοηθούμενης Αναπαραγωγής',
                'eProtocolName' => null,
                'eProtocolId' => '28'
            ],
            [
                'description' => 'Κανονική',
                'eProtocolName' => 'ΚΑΝΟΝΙΚΗ',
                'eProtocolId' => '3'
            ],
            [
                'description' => 'ΜΗΤΡΟΤΗΤΑΣ - Κανονική Κυοφορίας',
                'eProtocolName' => null,
                'eProtocolId' => '29'
            ],
            [
                'description' => 'ΜΗΤΡΟΤΗΤΑΣ - Κύησης',
                'eProtocolName' => null,
                'eProtocolId' => '30'
            ],
            [
                'description' => 'ΜΗΤΡΟΤΗΤΑΣ - Λοχείας',
                'eProtocolName' => null,
                'eProtocolId' => '31'
            ],
            [
                'description' => 'ΜΗΤΡΟΤΗΤΑΣ - Προγεννητικού Ελέγχου',
                'eProtocolName' => null,
                'eProtocolId' => '32'
            ],
            [
                'description' => 'Μονογονεϊκής οικογένειας (εκ χηρείας και στον άγαμο/η γονέα με επιμέλεια τέκνου) - ΙΣΧΥΕΙ ΜΟΝΟ ΓΙΑ ΑΝΑΠΛΗΡΩΤΕΣ',
                'eProtocolName' => null,
                'eProtocolId' => '33'
            ],
            [
                'description' => 'Ο.Τ.Α. - Για συνεδρίαση (σε εργάσιμες ώρες και ημέρες) Δημοτικού Συμβουλίου / Επιτροπών Δήμου / ΚΕΔΕ / ΠΕΔ',
                'eProtocolName' => null,
                'eProtocolId' => '34'
            ],
            [
                'description' => 'Παρακολούθησης σχολικής επίδοσης τέκνου',
                'eProtocolName' => 'ΠΑΡΑΚΟΛΟΥΘΗΣΗΣ ΣΧΟΛΙΚΗΣ ΕΠΙΔΟΣΗΣ',
                'eProtocolId' => '5'
            ],
            [
                'description' => 'Πατρότητας',
                'eProtocolName' => null,
                'eProtocolId' => '35'
            ],
            [
                'description' => 'ΣΥΝΔΙΚΑΛΙΣΤΙΚΗ - Πρόεδρος / Αντιπρόεδρος / Γεν. Γραμματέας πρωτοβάθμιας συνδικαλιστικής οργάνωσης με <500 μέλη (π.χ. ΕΛΜΕ)',
                'eProtocolName' => null,
                'eProtocolId' => '36'
            ],
            [
                'description' => 'ΣΥΝΔΙΚΑΛΙΣΤΙΚΗ - Πρόεδρος / Αντιπρόεδρος / Γεν. Γραμματέας πρωτοβάθμιας συνδικαλιστικής οργάνωσης με >=500 μέλη (π.χ. ΕΛΜΕ)',
                'eProtocolName' => null,
                'eProtocolId' => '37'
            ],
            [
                'description' => 'ΣΥΝΔΙΚΑΛΙΣΤΙΚΗ - Συμμετοχή σε συνέδριο αντιπροσώπου στις δευτεροβάθμιες και τριτοβάθμιες οργανώσεις και στα Ευρωπαϊκά Συμβούλια Εργαζομένων',
                'eProtocolName' => null,
                'eProtocolId' => '38'
            ]
        ];

        foreach ($leaveTypes as $type) {
            LeaveType::create($type);
        }
    }
}


    