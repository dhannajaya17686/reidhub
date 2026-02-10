# OTP Email Verification System - Implementation Documentation

## Overview

The OTP (One-Time Password) verification system has been implemented as part of the signup flow. Users now must verify their email address with a 6-digit code before accessing the dashboard.

## Flow Diagram

```
User Signup
    ↓
Create Account in DB
    ↓
Generate Random 6-digit OTP
    ↓
Save OTP to otps table (10-minute expiry)
    ↓
Send OTP via Email
    ↓
Redirect to /verify-email
    ↓
User Enters 6 Digits (auto-advance)
    ↓
Verify OTP (check code, expiry, attempts)
    ↓
Mark OTP as verified
    ↓
Create Session & Redirect to Dashboard
```

## Files Added/Modified

### New Files Created

1. **`sql/otp-creation.sql`** - Database schema for OTP storage
   - Stores OTP codes, expiry times, attempt counts
   - Includes indices for fast lookups and cleanup

2. **`app/helpers/EmailHelper.php`** - Email sending functionality
   - Supports native `mail()` and SMTP drivers
   - Beautiful HTML email templates for OTP and password reset
   - Follows ReidHub design system (blue theme, Poppins font)

3. **`app/helpers/RateLimiter.php`** - Rate limiting for security
   - File-based rate limit tracking
   - Prevents brute-force OTP verification attacks
   - Configurable attempt limits and time windows

4. **`app/views/Auth/verify-email-view.php`** - OTP verification form (redesigned)
   - 6 auto-advancing input fields
   - Matches sign-up page design (two-section layout, primary color)
   - Toast notifications for success/error
   - Resend button with 30-second countdown
   - Copy/paste support for OTP codes

### Modified Files

1. **`app/config/config.php`** - Added email and OTP configuration
   - Email driver (mail/smtp), sender details
   - OTP length, expiry, max attempts settings
   - SMTP configuration for future production use

2. **`app/models/User.php`** - Added OTP-related methods
   - `generateAndSaveOTP()` - Create and store OTP
   - `verifyOTPCode()` - Validate OTP with attempt tracking
   - `getLatestUnverifiedOTP()` - Retrieve pending OTP
   - `isEmailVerified()` - Check if email verified
   - `cleanupExpiredOTPs()` - Remove old records

3. **`app/controllers/Auth/LoginController.php`** - Added OTP controller methods
   - `showVerifyEmailForm()` - Display verification page
   - `sendOTP()` - Generate and send OTP (called from signup)
   - `verifyEmail()` - Validate OTP and create session
   - `resendOTP()` - Request new OTP (rate-limited)
   - Modified `signup()` to trigger OTP flow instead of direct login

4. **`app/routes/web.php`** - Added OTP routes
   - `/verify-email` (GET/POST) - Verification form and submission
   - `/resend-otp` (POST) - Resend OTP with rate limiting

## Configuration

### Email Settings (`app/config/config.php`)

```php
'MAIL_DRIVER' => 'mail',  // 'mail' (native) or 'smtp'
'MAIL_FROM_EMAIL' => 'noreply@reidhub.com',
'MAIL_FROM_NAME' => 'ReidHub',

// SMTP only (if using SMTP driver)
'SMTP_HOST' => 'smtp.mailtrap.io',
'SMTP_PORT' => 587,
'SMTP_USERNAME' => '',  // Set via env variables
'SMTP_PASSWORD' => '',  // Set via env variables
```

### OTP Settings (`app/config/config.php`)

```php
'OTP_LENGTH' => 6,                  // 6-digit codes
'OTP_EXPIRY_MINUTES' => 10,         // Valid for 10 minutes
'OTP_MAX_ATTEMPTS' => 5,            // Max 5 failed attempts
'OTP_RESEND_WAIT_SECONDS' => 30,    // 30-second wait between resends
```

## Database Setup

Run the migration to create the OTP table:

```bash
mysql -u reidhubuser -p reidhub < sql/otp-creation.sql
```

Or if using Docker:

```bash
docker exec reidhub-db mysql -u reidhubuser -preidhubpass reidhub < sql/otp-creation.sql
```

## Security Features

### 1. Rate Limiting
- **Signup Email Verification**: Max 5 failed attempts per email
- **OTP Resend**: Min 30-second wait between requests
- Uses file-based tracking for distributed systems

### 2. Attempt Tracking
- Counts failed verification attempts
- Blocks access after max attempts reached
- Attempts reset when OTP expires

### 3. Token Expiry
- All OTPs expire after 10 minutes
- Old tokens automatically cleaned up
- No valid OTP = no session creation

### 4. Input Validation
- Only numeric input accepted
- Six-digit requirement enforced
- Email validation before OTP generation

### 5. Session Security
- `session_regenerate_id(true)` called on successful verification
- Unverified email stored separately from user session
- Session cleaned up on logout

## Testing

### Manual Testing

1. **Signup Flow**
   ```
   1. Go to /signup
   2. Fill form and submit
   3. Should redirect to /verify-email
   4. Check email for OTP code
   5. Enter code and verify
   6. Should redirect to /dashboard/user
   ```

2. **Resend OTP**
   ```
   1. On /verify-email page
   2. Wait for countdown (30s)
   3. Click "Resend Code"
   4. Should receive new OTP
   5. Code updates in database
   ```

3. **Failed Attempts**
   ```
   1. Enter wrong OTP 5 times
   2. Should get "Too many attempts" error
   3. Must request new OTP
   ```

4. **OTP Expiry**
   ```
   1. Wait 10+ minutes
   2. Try to verify old OTP
   3. Should get "Invalid or expired" error
   ```

### Automated Testing (Future)

```php
// Example test case
$userModel = new User();
$otp = $userModel->generateAndSaveOTP('test@reidhub.com');
assert(strlen($otp) === 6);
assert($userModel->verifyOTPCode('test@reidhub.com', $otp) === true);
assert($userModel->verifyOTPCode('test@reidhub.com', '000000') === false);
```

## Frontend Experience

### OTP Input Fields
- **Auto-advance**: Typing a digit automatically moves to next field
- **Paste support**: Can paste full 6-digit code at once
- **Backspace handling**: Delete key goes back to previous field
- **Visual feedback**: Fields change color when filled/error

### Resend Feature
- **30-second countdown**: Shows "Retry in 30s"
- **Rate limiting**: Prevents spam requests
- **Success notification**: Toast shows "OTP resent successfully!"

### Error Handling
- Invalid code: "Invalid or expired OTP. Please try again."
- Too many attempts: "Too many failed attempts. Please request a new OTP."
- Expired: Automatically handled with attempt counter

### Email Template
- **Blue theme**: Matches ReidHub design (#0466C8)
- **Clear sections**: Logo, greeting, code display, warnings
- **Responsive**: Works on desktop and mobile
- **Warnings**: Reminds users never to share the code

## Performance Considerations

1. **Database Indices**: OTP table has indices on (email, expires_at) and created_at for fast queries
2. **Cleanup**: Expired OTPs can be cleaned via `User->cleanupExpiredOTPs()`
3. **Cron Job** (optional): Run cleanup periodically
   ```bash
   # Once daily
   0 0 * * * php /app/cleanup-otps.php
   ```

## Future Enhancements

1. **SMS Fallback**: Send OTP via SMS if email fails
2. **TOTP Support**: Switch to time-based OTP (like Google Authenticator)
3. **IP-based Rate Limiting**: Track by IP address as well
4. **Analytics**: Track verification success rates
5. **Template Customization**: Allow admins to customize email template
6. **Multi-factor Authentication**: Combine OTP with password reset

## Troubleshooting

### OTP Not Received
- Check MAIL_DRIVER configuration
- Test with: `php -r "mail('test@example.com', 'Test', 'Test message');"`
- Check server spam/junk folder
- Review logs in `storage/logs/app.log`

### OTP Verification Failing
- Check OTP hasn't expired (10 minute window)
- Verify email matches signup email
- Check failed attempt count (max 5)
- Try resending code after 30 seconds

### Database Errors
- Ensure `otps` table created via migration
- Check user has proper MySQL permissions
- Verify character encoding is utf8mb4

## Logging

All OTP events are logged to `storage/logs/app.log`:

```
[INFO] OTP generated and saved for email: user@example.com
[INFO] OTP sent to email: user@example.com
[INFO] Email sent successfully to: user@example.com
[INFO] OTP successfully verified for email: user@example.com
[WARNING] Invalid OTP attempt for email: user@example.com
[ERROR] Failed to send email to: user@example.com
```

## References

- RFC 4226: HOTP - An HMAC-Based One-Time Password Algorithm
- OWASP: Authentication Cheat Sheet (OTP best practices)
- ReidHub Architecture: See `docs/architecture.md`
- ReidHub API Reference: See `docs/api-reference.md`
