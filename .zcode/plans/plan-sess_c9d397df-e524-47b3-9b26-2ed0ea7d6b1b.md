## Forgot Password / Reset Password Implementation Plan

### New Files to Create (6):
1. **`app/Http/Controllers/Auth/PasswordResetLinkController.php`** — Show forgot-password form + send reset link email via Laravel's `Password::sendResetLink()`
2. **`app/Http/Controllers/Auth/NewPasswordController.php`** — Show reset-password form + reset password via Laravel's `Password::reset()`
3. **`app/Http/Requests/Auth/PasswordResetLinkRequest.php`** — Validate email field
4. **`app/Http/Requests/Auth/ResetPasswordRequest.php`** — Validate token, email, password (confirmed, min:8)
5. **`resources/views/auth/forgot-password.blade.php`** — Standalone page matching login style (emerald theme, theme/language switchers, x-form-input, x-button)
6. **`resources/views/auth/reset-password.blade.php`** — Same style with password + confirm password fields

### Existing Files to Modify (4):
1. **`routes/web.php`** — Add 4 password reset routes inside the existing `guest` middleware group
2. **`resources/views/auth/login.blade.php`** — Add "Forgot Password?" link
3. **`lang/en/app.php`** — Add `app.password_reset.*` English translation keys
4. **`lang/my/app.php`** — Add `app.password_reset.*` Myanmar translation keys

### New Localization Files (2):
5. **`lang/my/passwords.php`** — Myanmar translations for standard password reset messages
6. **`lang/my/auth.php`** — Myanmar translations for standard auth messages

### What's Already In Place (no changes):
- `password_reset_tokens` migration ✅
- User model with `Notifiable` trait ✅
- `config/auth.php` passwords broker ✅
- `lang/en/passwords.php` and `lang/en/auth.php` ✅

### Approach:
- Uses Laravel's built-in `Password` facade — no custom reset system
- Default `ResetPassword` notification — no custom notification class
- Views match exact same standalone HTML pattern as login (theme switcher, language switcher, emerald card)
- All strings use `__()` translation keys — full EN/Myanmar support
- Controllers follow same pattern as existing `LoginController`

### Mail:
- Default `MAIL_MAILER=log` works for dev (reset links in `storage/logs/laravel.log`)
- For real email: configure `MAIL_MAILER=smtp` + SMTP settings in `.env`