@component('mail::message')
# Hello {{ $user->name }},

A new login was detected on your **ExamPortal Online** account.

**Details:**
- Time: {{ $time }}
- IP Address: {{ $ip }}

If this was you, no action is needed.  
If this wasn’t you, please [reset your password]({{ route('password.request') }}) immediately.

Thanks,<br>
**ExamPortal Online Team**
@endcomponent