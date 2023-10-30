<?php

namespace Database\Seeders;

use App\Models\Consultant;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ConsultantsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Consultant::create([
            'name'=>'Χαράλαμπος',
            'surname'=>'Αλεξόπουλος',
            'afm'=>'035710901',
            'telephone'=>'6972111338',
            'mail'=>'balexop69@gmail.com',
            'klados'=>'ΠΕ70',
            'am'=>'563647',
            'md5'=>md5(563647035710901)
        ]);

        Consultant::create([
            'name'=>'Σοφία',
            'surname'=>'Ασημακοπούλου',
            'afm'=>'068450680',
            'telephone'=>'6906570525',
            'mail'=>'mfsasim@yahoo.gr',
            'klados'=>'ΠΕ70',
            'am'=>'601834',
            'md5'=>md5(601834068450680)
        ]);

        Consultant::create([
            'name'=>'Θεοφάνης',
            'surname'=>'Βαλμάς',
            'afm'=>'025784783',
            'telephone'=>'6977206177',
            'mail'=>'fvalmas@gmail.com',
            'klados'=>'ΠΕ70',
            'am'=>'550552',
            'md5'=>md5(550552025784783)
        ]);

        Consultant::create([
            'name'=>'Γεώργιος',
            'surname'=>'Ζάγκος',
            'afm'=>'033606837',
            'telephone'=>'6972303505',
            'mail'=>'gzagkos@sch.gr',
            'klados'=>'ΠΕ70',
            'am'=>'565098',
            'md5'=>md5(565098033606837)
        ]);

        Consultant::create([
            'name'=>'Θεοδώρα',
            'surname'=>'Μαρινάτου',
            'afm'=>'072366477',
            'telephone'=>'6970411590',
            'mail'=>'dmarinatou@gmail.com',
            'klados'=>'ΠΕ60',
            'am'=>'199662',
            'md5'=>md5(199662072366477)
        ]);

        Consultant::create([
            'name'=>'Αικατερίνη',
            'surname'=>'Νικολακοπούλου',
            'afm'=>'032066702',
            'telephone'=>'6976936318',
            'mail'=>'katnikolak@gmail.com',
            'klados'=>'ΠΕ70',
            'am'=>'555323',
            'md5'=>md5(555323032066702)
        ]);

        Consultant::create([
            'name'=>'Παναγιώτης',
            'surname'=>'Παπαδούρης',
            'afm'=>'036793761',
            'telephone'=>'6947824169',
            'mail'=>'panppap@hotmail.com',
            'klados'=>'ΠΕ70',
            'am'=>'578192',
            'md5'=>md5(578192036793761)
        ]);

        Consultant::create([
            'name'=>'Χρυσούλα',
            'surname'=>'Τσίρμπα',
            'afm'=>'075129846',
            'telephone'=>'',
            'mail'=>'tsirmpa.chrysoula@hotmail.com',
            'klados'=>'ΠΕ60',
            'am'=>'597261',
            'md5'=>md5(597261075129846)
        ]);

        Consultant::create([
            'name'=>'Βασιλική',
            'surname'=>'Φελούκα',
            'afm'=>'030613990',
            'telephone'=>'6974096364',
            'mail'=>'vfelouka@primedu.uoa.gr',
            'klados'=>'ΠΕ70',
            'am'=>'450848',
            'md5'=>md5(450848030613990)
        ]);

        Consultant::create([
            'name'=>'Νικόλαος',
            'surname'=>'Χολέβας',
            'afm'=>'036777750',
            'telephone'=>'6937701618',
            'mail'=>'nickleba1963@gmail.com',
            'klados'=>'ΠΕ70',
            'am'=>'578409',
            'md5'=>md5(578409036777750)
        ]);
    }
}
