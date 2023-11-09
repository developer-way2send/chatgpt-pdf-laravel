<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\PdfToText\Pdf;
use Illuminate\Support\Facades\Http;
use App\Models\PdfChatContent;

class PDFController extends Controller
{
    public function summarize(Request $request)
    {
        $request->validate([
            'pdf' => 'required|mimes:pdf',
            'question' => 'required',
        ]);

        // Store the uploaded PDF
        $pdfFile = $request->file('pdf');

        $pdfFilePath = $pdfFile->store('pdfs', 'public');
        $pdfPath = $request->file('pdf')->path();
        $pdfText = shell_exec("pdftotext $pdfPath -");
        $data = $request->all();
        $question = $request->question;
        $fileName = $pdfFile->getClientOriginalName();
        
        // Extract text from the PDF
        $text = Pdf::getText(storage_path('app/public/' . $pdfFilePath));
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.config('services.openai.api_key'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $text,
                ],
                [
                    'role' => 'user',
                    'content' => $question,
                ],
            ],
        ]);
       
        $summary = $response->json()['choices'][0]['message']['content'];
        if(!empty($summary)){
           
            $pdfChatContent= New PdfChatContent;
            $pdfChatContent->file_name=$fileName;
            $pdfChatContent->file_content=$text;
            $pdfChatContent->question=$question;
            $pdfChatContent->answer=$summary;
            $pdfChatContent->save();

        }
        return view('summary', compact('summary'));
    }
    public function OldApiCall(){
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.config('services.openai.api_key'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/engines/davinci/completions', [
            'prompt' => 'Summarize the following text: ' . $text,
            'max_tokens' => 500, // Adjust the length of the summary as needed
        ]);
       // echo "<Pre>";print_r ($response->json());exit;
        $summary = $response->json()['choices'][0]['text'];
    }
    public function showUploadForm()
    {
        return view('upload1');
    }

    public function uploadPDF(Request $request)
    {
        $request->validate([
            'pdf' => 'required|mimes:pdf',
        ]);

        $pdfPath = $request->file('pdf')->path();
        $pdfText = shell_exec("pdftotext $pdfPath -");

         
         // Set up your OpenAI API key
        OpenAI\OpenAI::setApiKey(config('services.openai.api_key'));

        // Call the GPT-3 API
        $response = OpenAI\OpenAI::createCompletion([
            'engine' => 'text-davinci-002',
            'prompt' => $pdfText,
            'max_tokens' => 100,
        ]);

        $generatedText = $response->choices[0]->text;

        return view('results', ['generatedText' => $generatedText]); 
    }
}
