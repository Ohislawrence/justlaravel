@component('mail::message')
{{-- Header with Logo --}}
<div style="text-align:center; padding:20px; background: linear-gradient(135deg, #6dab97, #10B981); border-radius: 6px 6px 0 0;">
    <img src="{{ asset('/images/logo.png') }}" alt="ExamPortal Online Logo" width="120" style="max-width:120px;">
</div>

{{-- Greeting --}}

### Welcome to {{ $organization->name }}, {{ $user->name }},

You are now registered with **{{ $organization->name }}**! 🎉  
Your account is almost ready. To finish setting up, please set your password:

@component('mail::button', ['url' => $resetUrl, 'color' => 'success'])
Set My Password
@endcomponent

If you do not know the organization, kindly ignore this email.

---

@component('mail::button', ['url' => route('examinee.dashboard'), 'color' => 'primary'])
Explore Your Dashboard
@endcomponent

---

### Next Steps
1. Set your password  
2. Check to see if your have been assigned an exam 

@component('mail::panel')
Need help getting started? 👉 Reach out to {{ $organization->official_email ?? $organization->name }}
@endcomponent

---

{{-- Render HTML footer --}}
{!! view('emails.exams.partials.footer')->render() !!}
@endcomponent
