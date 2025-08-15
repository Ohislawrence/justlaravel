<?php

namespace App\Jobs;

use App\Mail\GroupInvitationMail;
use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendGroupInvitation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $invitation;
    public $tries = 3; // Number of retry attempts
    public $maxExceptions = 3; // Max exceptions before marking as failed
    public $backoff = [60, 120, 300];

    /**
     * Create a new job instance.
     */
    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Mail::to($this->invitation->email)
            ->send(new GroupInvitationMail($this->invitation));
        
        // You could add retry logic here if needed
    }

    public function failed(\Throwable $exception)
    {
        // Log the failure or mark invitation as failed
        \Log::error("Failed to send invitation to {$this->invitation->email}: " . $exception->getMessage());
    }
}
