<?php

namespace App\Http\Controllers\microapps;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Env;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class EvaluationController extends Controller
{
    public function upload_file(Request $request)
    {
        $protocolResponse = $this->send_file_to_protocol($request);

        if($protocolResponse){
            return back()->with('success', 'το έντυπο υποβλήθηκε με επιτυχία.');
        } else {
            return back()->with('failure', 'Αποτυχία αποστολής εντύπου.');
        }
    }

    public function send_file_to_protocol(Request $request){
        //dd($request->file->path());
        $api_path = "/Evaluation/Director";
        $client = new Client([
            'debug' => fopen(\storage_path('logs/guzzle-debug.log'), 'w')
        ]);
        $full_url = config('services.directorate.url').$api_path;
        // Store the file temporarily
        //$tempPath = storage_path('app/temp/' . uniqid() . '_' . $request->file->getClientOriginalName());
        //$request->file->move(dirname($tempPath), basename($tempPath)); 
        $data = [
            ['name' => 'EmployeeAfm', 'contents' => $request->EmployeeAfm],
            ['name' => 'Stage', 'contents' => $request->Stage],
            ['name' => 'EvaluatorAfm', 'contents' => $request->EvaluatorAfm],
            ['name' => 'Status', 'contents' => $request->A2StatusName],
            ['name' => 'FileTitle', 'contents' => 'Δοκιμή'],
            [
            'name'     => 'file',
            'contents' => fopen($request->file('A2File')->path(), 'r'),
            'filename' => $request->file('A2File')->getClientOriginalName()
            ]
        ];
        //print_r($data);
        //dd($data);
        $response = $client->request('POST', $full_url, [
            'headers' => [
                'X-API-Key' => env('API_KEY'),
            ],
            'multipart' => $data,
        ]);
        // Get the response body
        $status = $response->getStatusCode();
        //$contents = $response->getBody()->getContents();
        if($status != 200){
            //dd($body);
            return false;
        } else {
            //print_r($contents);
            return $response;
        }
    }

    public function create()
    {
        if(Auth::guard('teacher')->check()){
            $afm = Auth::guard('teacher')->user()->afm;
            $API_response = $this->getEvaluatorData($afm, 'isTeacher');
            //dd($API_response->getBody()->getContents());
            $evaluation_data = $this->handleMultipartData($API_response);
            //dd($evaluation_data);
            return view('microapps.evaluation.create-director')->with(compact('evaluation_data'));
        }
            
        else if(Auth::guard('consultant')->check()){
            $afm = Auth::guard('consultant')->user()->afm;
            $API_response = $this->getEvaluatorData($afm, 'isConsultant');
            $evaluation_data = $this->handleMultipartData($API_response);
            if($evaluation_data !== "Error")
                return view('microapps.evaluation.create-consultant')->with(compact('evaluation_data'));
            else
            return back()->with('failure', 'Αδυναμία αποθήκευσης αρχείων.');   
        }
            
        abort(403, 'Unauthorized action.');
    }

    public function getEvaluatorData($afm, $whoIs)
    {
        if($whoIs == 'isTeacher') $api_path = '/evaluation/directorcurrent';
        if($whoIs == 'isConsultant') $api_path = '/evaluation/consultantcurrent';
        if(!$api_path) dd('error');
        $client = new Client([
            'debug' => fopen(\storage_path('logs/guzzle-debug.log'), 'w')
        ]);
        $full_url = config('services.directorate.url').$api_path;
        $response = $client->request('GET', config('services.directorate.url').$api_path, [
            'headers' => [
                'X-API-Key' => config('services.directorate.key'),
            ],
            'query' => [
                'afm' => $afm
            ]
        ]);
        // Get the response body
        $status = $response->getStatusCode();
        //$contents = $response->getBody()->getContents();
        if($status != 200){
            //dd($body);
            return false;
        } else {
            //print_r($contents);
            return $response;
        }
    }

    private function handleMultipartData($response)
    {    
        //Extract the Boundary
        $contentType = $response->getHeader('Content-Type')[0];
        preg_match('/boundary=(.*)/', $contentType, $matches);
        $boundary = trim($matches[1]);
        
        $body = $response->getBody()->getContents();
        //Use the boundary to split the response body into individual parts. Each part will have its own headers and content.
        $parts = explode("--$boundary", $body);
        //print_r($parts);
        // Remove the first and last elements (they are not actual parts)
        array_shift($parts);
        array_pop($parts);
        //dd($parts);

        $parsedParts = [];

        foreach ($parts as $part) {
            
            // Split headers and content
            list($headers, $content) = explode("\r\n\r\n", trim($part), 2);

            // Parse headers
            $headers = array_reduce(explode("\r\n", $headers), function ($carry, $header) {
                list($key, $value) = explode(':', $header, 2);
                $carry[strtolower(trim($key))] = trim($value);
                return $carry;
            }, []);

            // Add to parsed parts
            $parsedParts[] = [
                'headers' => $headers,
                'content' => trim($content),
            ];
        }
        $return_data = [];
        foreach ($parsedParts as $part) {
            $headers = $part['headers'];
            $content = $part['content'];
          
            // Example: Check Content-Type of the part
            if (isset($headers['content-type'])) {
                if (strpos($headers['content-type'], 'application/json') !== false) { // There is JSON data, get returned data
                    $return_data = json_decode($content, true);
                    
                } elseif (strpos($headers['content-type'], 'text/plain') !== false) { // There is text
                    // Process plain text
                } elseif (strpos($headers['content-type'], 'image/jpeg') !== false) {   // There is a jpeg image
                    
                } elseif (strpos($headers['content-type'], 'application/pdf') !== false) { // There is a pdf file
                    //dd($headers['content-disposition']);
                    // Define storage path (Laravel's storage directory)
                    //$decoded_parts = base64_decode($headers['content-disposition']);
                    //print_r($decoded_parts);
                    // Remove "=?" prefix and "?=" suffix
                    $string = str_replace('attachment; filename=?utf-8?B?', '', $headers['content-disposition']);
                    $string = str_replace('?=', '', $string);
                    print_r($string);
                    // Decode base64
                    //$decoded = base64_decode($string);
                    
                    // Convert to UTF-8 if needed
                    // if (!mb_check_encoding($decoded, 'UTF-8')) {
                    //     $decoded = mb_convert_encoding($decoded, 'UTF-8');
                    // }
                    dd("astop");
                    $fileName = 'document_' . $headers['content-type'] . '.pdf';
                    $filePath = storage_path('app/temp/'.$fileName);
                    try{
                        // Store the file
                        file_put_contents($filePath, $content);
                    }
                    catch(\Exception $e){
                        try{
                            Log::channel('files')->error("Save Evaluation file from API Response error: ".$e->getMessage());
                        }
                        catch(\Exception $e){
        
                        }
                        return "Error";     
                    }
                }
            }
        }
        return $return_data;
    }
}
