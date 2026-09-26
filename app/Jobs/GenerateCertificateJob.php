<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Event;
use App\Models\User;
use App\Services\PdfGeneratorService;
use App\Mail\CertificateMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class GenerateCertificateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly User $user,
        public readonly Event $event,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(PdfGeneratorService $pdfService): void
    {
        try {
            $pdfPath = $pdfService->generate($this->user, $this->event);

            Mail::to($this->user->email)->send(
                new CertificateMail($this->user, $this->event, $pdfPath)
            );

            Log::info("Certificate generated and email sent for user " . $this->user->id . " for event " . $this->event->id . "with email: {$this->user->email}.");

            // Notification Email
        } catch (Throwable $e) {
            Log::critical("Worker failed to generate certificate for job " . $this->job->getJobId() . ": " . $e->getMessage());
            $this->fail($e);
        }
    }
}
