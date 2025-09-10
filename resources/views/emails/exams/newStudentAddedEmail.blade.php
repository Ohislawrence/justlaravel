@component('mail::message')
{{-- Header with Logo --}}
<div style="text-align:center; padding:20px; background: linear-gradient(135deg, #6dab97, #10B981); border-radius: 6px 6px 0 0;">
    <img src="{{ asset('/images/logo.png') }}" alt="ExamPortal Online Logo" width="120" style="max-width:120px;">
</div>

{{-- Greeting --}}
### Hello, {{ $user->name }},

You have been added to {{ $organization->name }} by an admin.
Your account is almost ready. To finish setting up, please set your password:

@component('mail::button', ['url' => $resetUrl, 'color' => 'success'])
Set My Password
@endcomponent

If you do not know this organization, kindly ignore this email.

---

@component('mail::button', ['url' => route('examinee.dashboard'), 'color' => 'primary'])
Explore Your Dashboard
@endcomponent

---

### Next Steps
1. Set your password.
2. Log into your account. 
3. See if you have an exam/test to complete. 
4. Take you exam. 

@component('mail::panel')
Need help getting started? 👉 [View the Getting Started Guide]({{ route('help.index') }})
@endcomponent

---

{{-- Render HTML footer --}}
{!! view('emails.exams.partials.footer')->render() !!}
@endcomponent
