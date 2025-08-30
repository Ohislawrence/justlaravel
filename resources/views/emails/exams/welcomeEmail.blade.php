{{-- resources/views/emails/exams/welcomeEmail.blade.php --}}
@component('mail::message')
# Welcome to Examportal!

Hello {{ $user->name }},


@component('mail::panel')
## Your Login Details

- **Email:** {{ $user->email }}
- **Password:** 


@component('mail::subcopy')
This email was sent from Examportal. If you have any questions, please contact your administrator.
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent