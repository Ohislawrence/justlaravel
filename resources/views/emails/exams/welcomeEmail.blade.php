@component('mail::message')
{{-- Header with Logo --}}
<div style="text-align:center; padding:20px; background: linear-gradient(135deg, #6dab97, #10B981); border-radius: 6px 6px 0 0;">
    <img src="{{ asset('/images/logo.png') }}" alt="ExamPortal Online Logo" width="120" style="max-width:120px;">
</div>

{{-- Greeting --}}
### Welcome to ExamPortal Online, {{ $user->name }},

Thank you for joining **ExamPortal Online**! 🎉  
Your account is almost ready. To finish setting up, please set your password:

@component('mail::button', ['url' => $resetUrl, 'color' => 'success'])
Set My Password
@endcomponent

If you didn’t sign up, kindly ignore this email.

---

### Why Choose ExamPortal Online?

- ✅ **Easy Exam Creation** – Build professional exams in minutes  
- ✅ **Secure Proctoring** – Keep exams fair with monitoring tools  
- ✅ **Instant Results** – Automated grading and instant feedback  
- ✅ **Detailed Analytics** – Track results for individuals and groups  

@component('mail::button', ['url' => route('examiner.dashboard'), 'color' => 'primary'])
Explore Your Dashboard
@endcomponent

---

### Next Steps
1. Complete your organization profile  
2. Set up certificates if needed  
3. Create your first exam  
4. Invite team members or students to join  

@component('mail::panel')
Need help getting started? 👉 [View the Getting Started Guide]({{ route('help.index') }})
@endcomponent

---

{{-- Render HTML footer --}}
{!! view('emails.exams.partials.footer')->render() !!}
@endcomponent
