<?php

namespace App\Http\Controllers;

use App;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentController extends Controller
{
    // Display the upload form
    public function index()
    {
        return view('upload');
    }

    // Handle file upload
    public function upload(Request $request)
    {
        $request->validate([
            'document' => 'required|mimes:docx',
        ]);
    
        // Store the file in the 'public' disk, in 'documents' directory
        $path = $request->file('document')->store('documents', 'public');
        $filename = basename($path);  // Get the filename
    
        // Redirect to the edit page with the filename as ID
        return redirect()->route('edit', ['id' => $filename]);
    }
    

    // Edit the uploaded document
    public function edit($id)
    {
        // The correct file path is now in 'storage/app/public/documents/'
        $path = storage_path('app/public/documents/' . $id);
    
        if (!file_exists($path)) {
            return back()->withErrors(['message' => 'File not found!']);
        }
    
        try {
            // Load the .docx file from storage
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($path);
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Error loading the document: ' . $e->getMessage()]);
        }
    
        // Convert the document to HTML for editing
        $htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
        ob_start();
        $htmlWriter->save('php://output');
        $htmlContent = ob_get_clean();
    
        return view('edit', compact('id', 'htmlContent'));
    }
    
    // Update the document after editing
    public function update(Request $request, $id)
    {
        // Get the raw HTML content from the request
        $htmlContent = $request->input('content');
    
        // Sanitize the HTML using HTMLPurifier
        $config = \HTMLPurifier_Config::createDefault();
        $purifier = new \HTMLPurifier($config);
        $cleanHtmlContent = $purifier->purify($htmlContent);
    
        // Create a new PHPWord instance and add the sanitized HTML content
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();
    
        // Convert the sanitized HTML into a Word document
        \PhpOffice\PhpWord\Shared\Html::addHtml($section, $cleanHtmlContent);
    
        // Save the updated .docx file
        $path = storage_path('app/public/documents/' . $id);
        $phpWordWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $phpWordWriter->save($path);
    
        // Redirect to download the updated document
        return redirect()->route('download', ['id' => $id]);
    }

    // Download the edited document as .docx
    public function download($id)
    {
        // The file is stored in 'storage/app/public/documents'
        $path = storage_path('app/public/documents/' . $id);

        if (!file_exists($path)) {
            return back()->withErrors(['message' => 'File not found!']);
        }

        return response()->download($path);
    }

    // Convert and download the document as PDF
    public function downloadPdf($id)
    {
        // Load the .docx file from storage
        $path = storage_path('app/public/documents/' . $id);
        if (!file_exists($path)) {
            return back()->withErrors(['message' => 'File not found!']);
        }
    
        try {
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($path);
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Error loading the document: ' . $e->getMessage()]);
        }
    
        // Convert the .docx content to HTML for PDF rendering
        $htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
        ob_start();
        $htmlWriter->save('php://output');
        $htmlContent = ob_get_clean();
    
        // Generate the PDF using Pdf alias
        $pdf = Pdf::loadHTML($htmlContent);
    
        // Return the PDF download
        return $pdf->download($id . '.pdf');
    }

    public function htmlToPdf() {
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML('<h1>Test</h1>');

        return $pdf->stream();
    }
}
