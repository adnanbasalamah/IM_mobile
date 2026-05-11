<div class="login-wrapper">
    <div class="login-card">
        <div class="login-avatar">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
        </div>
        <h2 class="login-title">Login</h2>
        <p class="login-subtitle">Akses ke terminal IkhwanMart</p>

        <?php if (isset($_SESSION['login_error'])): ?>
            <div class="form-error"><?= htmlspecialchars($_SESSION['login_error']) ?></div>
            <?php unset($_SESSION['login_error']); ?>
        <?php endif; ?>

        <form class="login-form" method="POST" action="auth.php">
            <input type="hidden" name="action" value="login">
            <div class="form-group">
                <label class="form-label" for="username">Staff ID / Username</label>
                <div class="form-input-wrapper">
                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <input class="form-input" type="text" id="username" name="username" placeholder="Masukkan username" required autocomplete="username">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="form-input-wrapper">
                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <input class="form-input has-right-action" type="password" id="password" name="password" placeholder="Masukkan password" required autocomplete="current-password">
                    <button type="button" class="input-icon-right" onclick="togglePassword()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">
                Login
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M13.8 12H3"/></svg>
            </button>
        </form>

        <div class="login-footer">
            <p>Trusted retail management system</p>
            <div class="badges">
                <span class="badge badge-secure">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    Secure Access
                </span>
                <span class="badge badge-version">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><path d="M21 12a9 9 0 1 1-6.219-8.56"/><polyline points="22 2 22 8 16 8"/></svg>
                    v1.0
                </span>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const pw = document.getElementById('password');
    if (pw) pw.type = pw.type === 'password' ? 'text' : 'password';
}
</script>