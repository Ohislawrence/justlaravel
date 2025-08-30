<x-mail::message>
{{-- Header with logo --}}
<div style="text-align: center; margin-bottom: 30px;">
    <img src="{{ asset('images/logo.png') }}" alt="ExamPortal Logo" style="height: 60px;">
</div>

# Welcome to {{ $organization->name }}

You're managing the group **{{ $group->name }}** and have the following options to add members:

{{-- Registration Panel --}}
<x-mail::panel style="background-color: #f8fafc; border-left: 4px solid #10B981; border-radius: 8px; padding: 20px; margin: 20px 0;">
## Share Registration Link

Share this link with people you'd like to join your group:

<div style="background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px 16px; font-family: 'Courier New', monospace; font-size: 14px; word-break: break-all; margin: 16px 0;">
{{ $registrationLink }}
</div>

<x-mail::button :url="$registrationLink" color="success" style="background-color: #10B981; border-radius: 6px; padding: 12px 24px; font-size: 14px; font-weight: 600;">
Copy Registration Link
</x-mail::button>
</x-mail::panel>

{{-- Email Invitation Panel --}}
<x-mail::panel style="background-color: #f8fafc; border-left: 4px solid #3b82f6; border-radius: 8px; padding: 20px; margin: 20px 0;">
## Send Email Invitations

Send invitations directly to email addresses:

<div style="background: #f1f5f9; border-radius: 6px; padding: 12px; margin: 16px 0;">
<div style="display: flex; flex-wrap: wrap; gap: 8px;">
@foreach($emailList as $email)
<span style="background-color: #dbeafe; color: #1e40af; padding: 6px 12px; border-radius: 16px; font-size: 12px; font-weight: 500;">
{{ $email }}
</span>
@endforeach
</div>
</div>

<x-mail::button :url="route('examiner.groups.show', ['organization' => $organization->slug, 'group' => $group->id])" color="primary" style="background-color: #3b82f6; border-radius: 6px; padding: 12px 24px; font-size: 14px; font-weight: 600;">
Manage Group Members
</x-mail::button>
</x-mail::panel>

{{-- Footer --}}
<div style="background-color: #10B981; color: #ffffff; text-align: center; padding: 30px; border-radius: 8px; margin-top: 40px;">
    <h2 style="margin: 0 0 12px 0; font-size: 18px; font-weight: bold;">ExamPortal Online</h2>
    <p style="margin: 0 0 20px 0; font-size: 14px;">Helping schools, businesses, and organizations manage online exams with ease.</p>
    
    <div style="margin-bottom: 16px;">
        <a href="{{ url('/') }}" style="color: #ffffff; margin: 0 12px; text-decoration: none;">Home</a> |
        <a href="{{ url('/features') }}" style="color: #ffffff; margin: 0 12px; text-decoration: none;">Features</a> |
        <a href="{{ url('/pricing') }}" style="color: #ffffff; margin: 0 12px; text-decoration: none;">Pricing</a> |
        <a href="{{ url('/support') }}" style="color: #ffffff; margin: 0 12px; text-decoration: none;">Support</a>
    </div>

    <div style="margin-top: 16px;">
        <a href="https://facebook.com" style="margin: 0 8px;">
            <img src="https://cdn-icons-png.flaticon.com/24/733/733547.png" alt="Facebook">
        </a>
        <a href="https://twitter.com" style="margin: 0 8px;">
            <img src="https://cdn-icons-png.flaticon.com/24/733/733579.png" alt="Twitter">
        </a>
        <a href="https://linkedin.com" style="margin: 0 8px;">
            <img src="https://cdn-icons-png.flaticon.com/24/733/733561.png" alt="LinkedIn">
        </a>
    </div>

    <p style="margin-top: 20px; font-size: 12px; color: #d1fae5;">
        © {{ date('Y') }} ExamPortal Online. All rights reserved.
    </p>
</div>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>