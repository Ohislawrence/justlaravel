<x-mail::message>
# Welcome to {{ config('app.name') }}!

Hello {{ $user->name }},

We are excited to have you. Now that your account is fully activated, here's a brief introduction to our platform:

## What you can do:
- Create and manage quizzes
- Create question pools
- Use proctor
- Generate questions using AI
- Organize examinees into groups
- Track progress and results
- Generate certificates

## Getting Started:
1. Set up your organization profile
2. Create your first group of participants
3. Build your first quiz
4. Assign quizzes to your groups

If you have any questions, feel free to reach out to our support team.

Happy quizzing!<br>
The {{ config('app.name') }} Team
</x-mail::message>