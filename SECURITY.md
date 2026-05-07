# Security Policy

## Supported Scope

This package is a Symfony bundle for [`mirrorps/taler-php`](https://github.com/mirrorps/taler-php).

Security-sensitive behavior is provided by the upstream SDK, including:

- request construction and transport behavior
- authentication and token handling
- endpoint validation
- secret redaction in SDK debug logging

This package is responsible for Symfony integration concerns, including:

- mapping `taler` bundle configuration into SDK options
- constructing the HTTP client used by the SDK, including transport defaults
- registering Symfony services and aliases 

## Reporting A Vulnerability

Please do not open public issues for suspected security vulnerabilities.

Use GitHub's private vulnerability reporting for this repository and include:

- a clear description of the issue
- affected package version(s)
- steps to reproduce
- impact assessment
- any suggested remediation

## Security Notes

- Use `token` authentication in production where possible. If using username/password, provide them via Symfony secrets or environment variables instead of committing credentials.
- Keep `base_url` on HTTPS endpoints you control and trust.
- Apply least privilege by setting the narrowest `scope` needed for the integration.
