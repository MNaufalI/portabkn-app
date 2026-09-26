<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Event;
use App\Models\User;
use FPDF;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class PdfGeneratorService
{
    /**
     * @throws Exception
     */
    public function generate(User $user, Event $event): string
    {
        try {
            // 1. Identity Asset
            $fileName = "certificates/{event->id}_{$user->id}.pdf";

            //2. 
            if (Storage::disk('public')->exists($fileName)) {
                Log::info("Idempotent Check: Certificate already exists for user {$user->id}");
            }

            // 3. Generate PDF if it doesn't exist
            $pdf = new FPDF('L', 'mm', 'A4');
            $pdf->AddPage();

            $templatePath = public_path('images/template_sertifikat.jpg');
            if (!file_exists($templatePath)) {
                throw new Exception("Template file not found at path: {$templatePath}");
            }

            $pdf->Image($templatePath, 0, 0, 297, 210);

            $pdf->SetFont('Arial', 'B', 28);
            $pdf->SetTextColor(30, 30, 30);
            $pdf->SetXY(0, 100); 
            $pdf->Cell(297, 10, strtoupper($user->name), 0, 1, 'C');

            $pdf->SetFont('Arial', '', 16);
            $pdf->SetTextColor(100, 100, 100);
            $pdf->SetXY(0, 120);
            $pdf->Cell(297, 10, "Has completed the event: " . $event->title, 0, 1, 'C');
            
            $pdfBinary = $pdf->Output('S');

            // 4. Save the PDF to storage
            Storage::disk('public')->put($fileName, $pdfBinary);
            Log::info("Certificate saved in storage: {$fileName}");

            return $fileName;

        } catch (Exception $e) {
            Log::error("Failed to generate PDF for user {$user->id} and event {$event->id}: " . $e->getMessage());
            throw $e;    
        }
    }
}