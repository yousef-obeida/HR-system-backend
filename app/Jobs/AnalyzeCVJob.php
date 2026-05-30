<?php

namespace App\Jobs;

use App\Ai\Agents\CVAnalyzer;
use App\Models\Candidate;
use App\Models\CvAnalysis;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;

class AnalyzeCVJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying.
     */
    public int $backoff = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Candidate $candidate
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Step 7 — Extract CV text from PDF
        $cvFullPath = storage_path('app/public/' . $this->candidate->cv_path);

        if (!file_exists($cvFullPath)) {
            Log::error("CV file not found for candidate #{$this->candidate->id}: {$cvFullPath}");
            return;
        }

        try {
            // smalot/pdfparser is a pure-PHP library, so CV text extraction works
            // identically across platforms (Linux VPS, Windows dev) with no
            // external binary dependency.
            $text = (new Parser())->parseFile($cvFullPath)->getText();
        } catch (\Throwable $e) {
            Log::error("Failed to extract text from CV for candidate #{$this->candidate->id}: {$e->getMessage()}");
            return;
        }

        if (empty(trim($text))) {
            Log::warning("No text extracted from CV for candidate #{$this->candidate->id}");
            return;
        }

        // Step 9 — Analyze CV with AI agent
        $result = CVAnalyzer::make()->prompt($text);

        // Step 11.4 — Validate AI response
        if (
            !isset($result['summary']) ||
            !isset($result['skills']) ||
            !isset($result['experience_years']) ||
            !isset($result['score']) ||
            !isset($result['recommendation'])
        ) {
            Log::error("Invalid AI response for candidate #{$this->candidate->id}");
            throw new \Exception('Invalid AI response — missing required fields');
        }

        // Step 11.2 — Store analysis result (updateOrCreate avoids duplicates)
        CvAnalysis::updateOrCreate(
            ['candidate_id' => $this->candidate->id],
            [
                'summary' => $result['summary'],
                'skills' => $result['skills'],
                'experience_years' => $result['experience_years'],
                'score' => $result['score'],
                'recommendation' => $result['recommendation'],
            ]
        );

        Log::info("CV analysis completed for candidate #{$this->candidate->id} — Score: {$result['score']}, Recommendation: {$result['recommendation']}");
    }
}
