<x-mail::message>
# Welcome to {{ config('app.name') }}

Hello {{ $user->name }},

Thank you for registering with {{ config('app.name') }}! We're excited to have you on board.

To get started, please verify your email address by clicking the button below:



If you didn't create an account, no further action is required.

Thanks,<br>
The {{ config('app.name') }} Team
</x-mail::message>