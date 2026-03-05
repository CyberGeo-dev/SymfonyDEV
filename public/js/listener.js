$(document).ready(function () {

    // -------------------------
    // Regex
    // -------------------------
    const passwordRegex = new RegExp("^(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,}$");
    const mailRegex     = new RegExp("^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}$");

    // -------------------------
    // Elements
    // -------------------------
    const noError              = document.getElementById('noError');
    const passwordError        = document.getElementById('passwordError');
    const passwordMatches      = document.getElementById('passwordMatches');
    const submitButton         = document.getElementById('formUserSubmit');
    const usernameInput        = document.getElementById('users_username');
    const passwordInput        = document.getElementById('users_password');
    const confirmPasswordInput = document.getElementById('users_confirm_password');
    const mailInput            = document.getElementById('users_mail');

    // -------------------------
    // Flags
    // -------------------------
    let okUsername      = false;
    let okPasswordRegex = false;
    let okPasswordMatch = false;
    let okMail          = true; // mail vide = ok (pas obligatoire)

    // -------------------------
    // Init
    // -------------------------
    submitButton.disabled  = true;
    noError.hidden         = true;
    passwordError.hidden   = true;
    passwordMatches.hidden = true;

    // -------------------------
    // Helper : activer/désactiver le bouton
    // -------------------------
    function refreshSubmit() {
        submitButton.disabled = !(okUsername && okPasswordRegex && okPasswordMatch && okMail);
    }

    // -------------------------
    // Helper : classes is-valid / is-invalid
    // -------------------------
    function validInvalidCss(input, isValid, removeAll = false) {
        if (!input) return;
        if (removeAll) {
            input.classList.remove('is-valid');
            input.classList.remove('is-invalid');
            return;
        }
        if (isValid) {
            input.classList.add('is-valid');
            input.classList.remove('is-invalid');
        } else {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
        }
    }

    // -------------------------
    // USERNAME — blur + AJAX
    // -------------------------
    usernameInput.addEventListener('blur', function () {

        const usernameValue = usernameInput.value.trim();

        if (usernameValue === '') {
            noError.hidden = true;
            okUsername = false;
            validInvalidCss(usernameInput, false);
            refreshSubmit();
            return;
        }

        $.ajax({
            type: 'POST',
            url: '/account/check',
            data: { username: usernameValue },
            async: true,
            dataType: 'json'
        }).done(function (data) {

            // jQuery avec dataType:'json' parse déjà la réponse
            // Mais le controller renvoie new JsonResponse($json, 200, [], true)
            // ce qui peut double-encoder => on tente un parse si c'est une string
            let user = data;
            if (typeof data === 'string') {
                try { user = JSON.parse(data); } catch (e) { user = null; }
            }

            if (user !== null) {
                noError.hidden = false;
                okUsername = false;
                validInvalidCss(usernameInput, false);
            } else {
                noError.hidden = true;
                okUsername = true;
                validInvalidCss(usernameInput, true);
            }

            refreshSubmit();

        }).fail(function () {
            noError.hidden = false;
            okUsername = false;
            validInvalidCss(usernameInput, false);
            refreshSubmit();
        });
    });

    // -------------------------
    // PASSWORD REGEX — keyup
    // -------------------------
    function checkPasswordRegex() {
        const val = passwordInput.value;

        if (val === '') {
            passwordMatches.hidden = true;
            okPasswordRegex = false;
            validInvalidCss(passwordInput, false, true); // neutre si vide
            return;
        }

        if (passwordRegex.test(val)) {
            passwordMatches.hidden = true;
            okPasswordRegex = true;
            validInvalidCss(passwordInput, true);
        } else {
            passwordMatches.hidden = false;
            okPasswordRegex = false;
            validInvalidCss(passwordInput, false);
        }
    }

    // -------------------------
    // PASSWORD MATCH — keyup
    // -------------------------
    function checkPasswordMatch() {
        const p1 = passwordInput.value;
        const p2 = confirmPasswordInput.value;

        if (p2 === '') {
            // champ pas encore touché => neutre, pas d'erreur
            passwordError.hidden = true;
            okPasswordMatch = false;
            validInvalidCss(confirmPasswordInput, false, true);
            return;
        }

        if (p1 === p2) {
            passwordError.hidden = true;
            okPasswordMatch = true;
            validInvalidCss(confirmPasswordInput, true);
        } else {
            passwordError.hidden = false;
            okPasswordMatch = false;
            validInvalidCss(confirmPasswordInput, false);
        }
    }

    passwordInput.addEventListener('keyup', function () {
        checkPasswordRegex();
        checkPasswordMatch();
        refreshSubmit();
    });

    confirmPasswordInput.addEventListener('keyup', function () {
        checkPasswordMatch();
        refreshSubmit();
    });

    // -------------------------
    // MAIL — blur
    // -------------------------
    mailInput.addEventListener('blur', function () {

        const val = mailInput.value.trim();

        if (val === '') {
            // mail vide => neutre (champ optionnel)
            okMail = true;
            validInvalidCss(mailInput, false, true);
        } else if (mailRegex.test(val)) {
            okMail = true;
            validInvalidCss(mailInput, true);
        } else {
            okMail = false;
            validInvalidCss(mailInput, false);
        }

        refreshSubmit();
    });

});