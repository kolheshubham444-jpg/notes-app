// ── Field validators — each returns an error string or ''
const validators = {
    username(val) {
        if (!val)          return 'Username is required.';
        if (val.length < 3) return 'Minimum 3 characters.';
        if (val.length > 50) return 'Maximum 50 characters.';
        if (!/^[a-zA-Z0-9_]+$/.test(val)) return 'Only letters, numbers, and underscores allowed.';
        return '';
    },
    email(val) {
        if (!val) return 'Email is required.';
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) return 'Enter a valid email address.';
        return '';
    },
    password(val) {
        if (!val)           return 'Password is required.';
        if (val.length < 6) return 'Minimum 6 characters.';
        return '';
    },
    confirm(val, form) {
        if (!val) return 'Please confirm your password.';
        const pwd = form && form.querySelector('[name="password"]');
        if (pwd && val !== pwd.value) return 'Passwords do not match.';
        return '';
    },
    title(val) {
        if (!val)           return 'Note title is required.';
        if (val.length < 2) return 'Title must be at least 2 characters.';
        if (val.length > 255) return 'Title must be under 255 characters.';
        return '';
    },
};

const ALLOWED_EXTS = ['jpg','jpeg','png','gif','pdf','doc','docx','txt','xlsx','xls','ppt','pptx','zip'];
const MAX_BYTES    = 5 * 1024 * 1024; // 5 MB

function validateFile(input) {
    if (input.required && (!input.files || !input.files.length))
        return 'Please select a file.';
    if (input.files && input.files.length) {
        const ext  = input.files[0].name.split('.').pop().toLowerCase();
        if (!ALLOWED_EXTS.includes(ext))
            return 'Allowed types: PDF, image, Word, Excel, TXT, ZIP.';
        if (input.files[0].size > MAX_BYTES)
            return 'File must be under 5 MB.';
    }
    return '';
}

// ── DOM helpers
function msgEl(input) {
    let el = input.nextElementSibling;
    if (el && el.classList.contains('v-msg')) return el;
    el = document.createElement('span');
    el.className = 'v-msg';
    input.after(el);
    return el;
}

function setError(input, msg) {
    input.classList.add('input-error');
    input.classList.remove('input-valid');
    const el = msgEl(input);
    el.textContent = msg;
    el.style.display = 'block';
}

function setValid(input) {
    input.classList.remove('input-error');
    input.classList.add('input-valid');
    const el = input.nextElementSibling;
    if (el && el.classList.contains('v-msg')) {
        el.textContent = '';
        el.style.display = 'none';
    }
}

function checkField(input, form) {
    const name = input.name;
    let error = '';

    if (input.type === 'file') {
        error = validateFile(input);
    } else if (validators[name]) {
        error = validators[name](input.value.trim(), form);
    } else {
        return true;
    }

    if (error) { setError(input, error); return false; }
    setValid(input);
    return true;
}

// ── Password strength meter
const STRENGTH_LABELS = ['', 'Very Weak', 'Weak', 'Fair', 'Strong', 'Very Strong'];
const STRENGTH_COLORS = ['', '#ef4444', '#f97316', '#eab308', '#22c55e', '#16a34a'];

function calcScore(val) {
    let s = 0;
    if (val.length >= 6)             s++;
    if (val.length >= 10)            s++;
    if (/[A-Z]/.test(val))           s++;
    if (/[0-9]/.test(val))           s++;
    if (/[^A-Za-z0-9]/.test(val))   s++;
    return s;
}

function injectStrengthMeter(pwdInput) {
    const wrap = document.createElement('div');
    wrap.className = 'strength-wrap';
    wrap.innerHTML =
        `<div class="strength-track"><div class="strength-fill" id="s-fill"></div></div>` +
        `<span class="strength-label" id="s-label"></span>`;
    pwdInput.after(wrap);

    pwdInput.addEventListener('input', () => {
        const score = calcScore(pwdInput.value);
        const fill  = document.getElementById('s-fill');
        const label = document.getElementById('s-label');
        fill.style.width      = (score * 20) + '%';
        fill.style.background = STRENGTH_COLORS[score];
        label.textContent     = pwdInput.value ? STRENGTH_LABELS[score] : '';
        label.style.color     = STRENGTH_COLORS[score];
    });
}

// ── Boot
document.addEventListener('DOMContentLoaded', () => {

    // Inject strength meter only on register form (has both password + confirm)
    const pwdInput = document.querySelector('[name="password"]');
    if (pwdInput && document.querySelector('[name="confirm"]')) {
        injectStrengthMeter(pwdInput);
    }

    document.querySelectorAll('form').forEach(form => {

        form.querySelectorAll('input').forEach(input => {

            // Validate on blur
            input.addEventListener('blur', () => checkField(input, form));

            // Re-validate while typing if field already has an error
            input.addEventListener('input', () => {
                if (input.classList.contains('input-error')) checkField(input, form);
            });

            // When password changes, re-check confirm if it was already touched
            if (input.name === 'password') {
                input.addEventListener('input', () => {
                    const confirm = form.querySelector('[name="confirm"]');
                    if (confirm && (confirm.classList.contains('input-error') ||
                                    confirm.classList.contains('input-valid'))) {
                        checkField(confirm, form);
                    }
                });
            }
        });

        // Block submit if any field fails
        form.addEventListener('submit', e => {
            let valid = true;
            form.querySelectorAll('input').forEach(input => {
                if (!checkField(input, form)) valid = false;
            });
            if (!valid) e.preventDefault();
        });
    });
});
