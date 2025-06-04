document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("encryption").addEventListener("change", togglePasswordField);
    document.getElementById("createBtn").addEventListener("click", handleCreateButtonClick);
    document.getElementById("copyBtn").addEventListener("click", copyShortenedLink);
    document.getElementById('modalCopyBtn').addEventListener('click', () => copyToClipboard('modalUrl', 'Shortlink berhasil disalin!'));
    document.getElementById('modalPasswordCopyBtn').addEventListener('click', () => copyToClipboard('modalPassword', 'Password berhasil disalin!'));
    document.getElementById('saveAsPngBtn').addEventListener('click', saveQRCodeAsPNG);

    const urlModal = document.getElementById('urlModal');
    urlModal.addEventListener('hidden.bs.modal', function () {
        window.location.href = '/myurl';
    });

    setupTooltips();
    setupDragAndDrop();

    const oldResetForm = resetForm;
    resetForm = function() {
        oldResetForm();
        turnstile.reset();
    };
});

function togglePasswordField() {
    var passwordField = document.getElementById("passwordField");
    passwordField.style.display = this.checked ? "block" : "none";
}

let turnstileToken = null;

function handleTurnstileCallback(token) {
    console.log('Turnstile token received');
    turnstileToken = token;
}

function handleTurnstileError(error) {
    console.error('Turnstile error:', error);
    showErrorModal("Terjadi kesalahan pada verifikasi keamanan. Silakan coba lagi.");
}

async function handleCreateButtonClick() {
    var original_url = document.getElementById("original_url").value;
    var alias_url = document.getElementById("alias_url").value;
    var shortenedLinkInput = document.getElementById("shortened_url");
    var expiry = document.getElementById("expiry").value;

    if (original_url === "") {
        showErrorModal("Mohon isi original URL");
        return;
    }

    if (!isValidUrl(original_url)) {
        showErrorModal("URL tidak valid");
        return;
    }

    if (shortenedLinkInput.value !== "") {
        showErrorModal("Anda sudah membuat URL.");
        return;
    }

    if (alias_url.trim() !== "") {
        if (/\s/.test(alias_url)) {
            showErrorModal("Customize URL tidak boleh mengandung spasi.");
            return;
        }

        var invalidChar = validateAlias(alias_url);
        if (invalidChar) {
            showErrorModal(`Customize URL tidak boleh mengandung "${invalidChar}"`);
            return;
        }

        var aliasLengthError = validateAliasLength(alias_url);
        if (aliasLengthError) {
            showErrorModal(aliasLengthError);
            return;
        }
    }

    var encryption = document.getElementById("encryption").checked;
    var password = document.getElementById("password").value;

    if (encryption && password.trim() === "") {
        showErrorModal("Password harus diisi jika memilih enkripsi.");
        return;
    }

    if (!expiry) {
        showErrorModal("Pilih waktu expired untuk shortlink.");
        return;
    }

    if (!turnstileToken) {
        console.log('No Turnstile token available');
        showErrorModal("Mohon selesaikan verifikasi keamanan terlebih dahulu");
        return;
    }

    try {
        document.getElementById('loaderContainer').style.display = 'flex';
        document.getElementById('createBtn').disabled = true;

        sendShortenRequest(
            original_url,
            alias_url,
            encryption,
            password,
            expiry,
            turnstileToken
        );
    } catch (error) {
        console.error('Error:', error);
        showErrorModal("Terjadi kesalahan. Silakan coba lagi.");
    } finally {
        document.getElementById('createBtn').disabled = false;
    }
}

function sendShortenRequest(original_url, alias_url, encryption, password, expiry, token) {
    document.getElementById('loaderContainer').style.display = 'flex';

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/shortener/shorten", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    const csrfToken = document.querySelector('input[name="csrf_token"]').value;
    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4) {
            document.getElementById('loaderContainer').style.display = 'none';
            if (xhr.status == 200) {
                handleShortenResponse(JSON.parse(xhr.responseText), encryption, password);
            } else {
                showErrorModal("Gagal. Mohon coba lagi");
            }
        }
    };

    var params = "original_url=" + encodeURIComponent(original_url) +
        "&alias_url=" + encodeURIComponent(alias_url) +
        "&encryption=" + (encryption ? 1 : 0) +
        "&password=" + (encryption ? encodeURIComponent(password) : '') +
        "&expiry=" + encodeURIComponent(expiry) +
	"&cf-turnstile-response=" + encodeURIComponent(token);
	"&csrf_token=" + encodeURIComponent(csrfToken);

    xhr.send(params);
}

function handleShortenResponse(response, encryption, password) {
    if (response.error) {
        if (response.unsafe) {
            showVirusTotalModal();
        } else if (response.showModal) {
            showDuplicateAliasModal(response.modalMessage);
        } else {
            showErrorModal(response.error);
        }
    } else {
        document.getElementById("shortened_url").value = response.shortened_url;
        document.getElementById("shortenedLinkGroup").style.display = 'flex';
        document.getElementById('modalUrl').value = response.shortened_url;

        generateQRCode(response.shortened_url);

        if (encryption) {
            document.getElementById('passwordGroup').style.display = 'block';
            document.getElementById('modalPassword').value = response.password || password;
        } else {
            document.getElementById('passwordGroup').style.display = 'none';
        }

        new bootstrap.Modal(document.getElementById('urlModal')).show();
        resetForm();
    }
}

function resetForm() {
    document.getElementById('original_url').value = "";
    document.getElementById('alias_url').value = "";
    document.getElementById('encryption').checked = false;
    document.getElementById('password').value = "";
    document.getElementById('expiry').selectedIndex = 0;
    document.getElementById('passwordField').style.display = 'none';
}

function copyShortenedLink() {
    var shortenedLinkInput = document.getElementById("shortened_url");
    shortenedLinkInput.select();
    document.execCommand("copy");
}

function copyToClipboard(elementId, successMessage) {
    var copyText = document.getElementById(elementId);
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    alert(successMessage);
}

function validateAlias(alias) {
    var invalidChars = alias.match(/[^a-z0-9~%.:_\-]/gi);
    return invalidChars ? invalidChars[0] : null;
}

function isValidUrl(url) {
    var pattern = /^https?:\/\/.+/;
    return pattern.test(url);
}

function validateAliasLength(alias) {
    if (alias.trim() === "") return null;
    if (alias.length < 4) return "Customize URL minimal 4 karakter";
    if (alias.length > 75) return "Customize URL maksimal 75 karakter";
    return null;
}

function showErrorModal(message) {
    document.getElementById('aliasErrorMessage').textContent = message;
    new bootstrap.Modal(document.getElementById('aliasErrorModal')).show();
}

function showVirusTotalModal() {
    new bootstrap.Modal(document.getElementById('virusTotalModal')).show();
}

function showDuplicateAliasModal(message) {
    document.getElementById('duplicateAliasModalBody').textContent = message;
    new bootstrap.Modal(document.getElementById('duplicateAliasModal')).show();
}

function generateQRCode(url) {
    var qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=" + encodeURIComponent(url);

    var qrCodeImage = new Image();
    qrCodeImage.crossOrigin = 'Anonymous';
    qrCodeImage.src = qrCodeUrl;

    qrCodeImage.onload = function () {
        var canvas = document.createElement('canvas');
        var ctx = canvas.getContext('2d');

        canvas.width = 400;
        canvas.height = 400;

        ctx.drawImage(qrCodeImage, 0, 0, 400, 400);

        var logo = new Image();
        logo.src = '/assets/images/logo/pu.jpg';
        logo.onload = function () {
            var logoSize = 80;
            var x = (canvas.width - logoSize) / 2;
            var y = (canvas.height - logoSize) / 2;
            ctx.drawImage(logo, x, y, logoSize, logoSize);

            document.getElementById('qrCodeImage').src = canvas.toDataURL();
        };
    };
}

function setupDragAndDrop() {
    const modalUrl = document.getElementById('modalUrl');

    modalUrl.addEventListener('dragstart', function (event) {
        event.dataTransfer.setData('text/plain', modalUrl.value);
    });

    document.addEventListener('dragover', function (event) {
        event.preventDefault();
    });

    document.addEventListener('drop', function (event) {
        event.preventDefault();
        const droppedData = event.dataTransfer.getData('text');
        console.log('Dropped text:', droppedData);
    });
}

function setupTooltips() {
    var tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl, {
            trigger: 'hover',
            delay: { show: 0, hide: 100 }
        });
    });
}

function saveQRCodeAsPNG() {
    const qrImage = document.getElementById('qrCodeImage');
    const imageUrl = qrImage.src;

    const a = document.createElement('a');
    a.href = imageUrl;
    a.download = 'Shortlink.png';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}
