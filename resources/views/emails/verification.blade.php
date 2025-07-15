<h1>Verify Your Email</h1>
<p>Hello {{ $user->name }},</p>
<p>Please click the link below to verify your email address:</p>

<a href="{{ route('verify.email', ['token' => $user->verification_token]) }}">
    Verify Email
</a>

<p>Thank you!</p>
