function showLoading() {
    document.getElementById('loadingOverlay').classList.remove('d-none');
}

function hideLoading() {
    document.getElementById('loadingOverlay').classList.add('d-none');
}

function showErrorModal(message) {
    document.getElementById('aliasErrorMessage').textContent = message;
    new bootstrap.Modal(document.getElementById('aliasErrorModal')).show();
}

function showSuccessModal(message) {
    document.getElementById('editSuccessMessage').textContent = message;
    new bootstrap.Modal(document.getElementById('editSuccessModal')).show();
}

function showVirusTotalModal() {
    new bootstrap.Modal(document.getElementById('virusTotalModal')).show();
}

function showDuplicateAliasModal(message) {
    document.getElementById('duplicateAliasModalBody').textContent = message;
    new bootstrap.Modal(document.getElementById('duplicateAliasModal')).show();
}

function showUrlModal() {
    const urlModal = new bootstrap.Modal(document.getElementById('urlModal'));

    document.getElementById('urlModal').addEventListener('hidden.bs.modal', function () {
        window.location.reload();
    }, { once: true });

    urlModal.show();
}

let turnstileToken = null;
let isProcessing = false;

function handleTurnstileCallback(token) {
    console.log('Turnstile token received');
    turnstileToken = token;
}

function handleTurnstileError(error) {
    console.error('Turnstile error:', error);
    showErrorModal("Terjadi kesalahan pada verifikasi keamanan. Silakan coba lagi.");
}

        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById("encryption").addEventListener("change", function () {
                var passwordField = document.getElementById("passwordField");
                passwordField.style.display = this.checked ? "block" : "none";
            });

            document.getElementById("createBtn").addEventListener("click", async function () {
                // e.preventDefault();
	     try {
		console.log("Tombol create ditekan");

                var original_url = document.getElementById("original_url").value;
                var alias_url = document.getElementById("alias_url").value;
                var shortenedLinkInput = document.getElementById("shortened_url");
                var expiry = document.getElementById("expiry").value;

		if (isProcessing) {
                   return;
            	}
            	isProcessing = true;

                if (original_url.trim() === "") {
                    showErrorModal("Mohon isi Original URL");
                    return;
                }

                if (!isValidUrl(original_url)) {
                    showErrorModal("URL tidak valid");
                    return;
                }

                if (shortenedLinkInput.value !== "") {
                    showErrorModal("Anda sudah membuat URL");
                    return;
                }

		if (alias_url.trim() !== "") {
                    if (/\s/.test(alias_url)) {
                        showErrorModal("Customize URL tidak boleh mengandung spasi");
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
                    showErrorModal("Masukkan Kata Sandi untuk enkripsi");
                    return;
                }

                if (!expiry) {
                    showErrorModal("Mohon pilih waktu Masa Berlaku Shortlink");
                    return;
                }

		if (!turnstileToken) {
            console.log('No Turnstile token available');
            showErrorModal("Mohon selesaikan verifikasi keamanan terlebih dahulu");
            isProcessing = false;
            return;
        }

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
                isProcessing = false;

                try {
                    if (xhr.status == 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.error) {
                            if (response.unsafe) {
                                showVirusTotalModal();
                            } else {
                                showErrorModal(response.error);
                            }
                        } else {
                            shortenedLinkInput.value = response.shortened_url;
                            document.getElementById('modalUrl').value = response.shortened_url;

                            if (encryption) {
                                document.getElementById('passwordGroup').style.display = 'block';
                                document.getElementById('modalPassword').value = response.password || password;
                            }

                            generateQRCode({
                                url: response.shortened_url,
                                size: 200,
                                imageId: 'qrCodeImage'
                            });

                            showUrlModal();

                            document.getElementById('shortenForm').reset();
                            document.getElementById('passwordField').style.display = 'none';
                            turnstile.reset();
                            turnstileToken = null;
                        }
                    } else {
                        showErrorModal("Gagal. Mohon dicoba lagi");
                    }
                } catch (error) {
                    console.error("Error processing response:", error);
                    showErrorModal("Terjadi kesalahan saat memproses response");
                }
            }
        };

        var params = "original_url=" + encodeURIComponent(original_url) +
            "&alias_url=" + encodeURIComponent(alias_url) +
            "&encryption=" + (encryption ? 1 : 0) +
            "&password=" + (encryption ? encodeURIComponent(password) : '') +
            "&expiry=" + encodeURIComponent(expiry) +
            "&cf-turnstile-response=" + encodeURIComponent(turnstileToken);
	    "&csrf_token=" + encodeURIComponent(csrfToken);

        xhr.send(params);

        } catch (error) {
            console.error("Error in create button handler:", error);
            showErrorModal("Terjadi kesalahan");
            isProcessing = false;
        }
    });

	     document.getElementById("copyBtn").addEventListener("click", function () {
                copyToClipboard('modalUrl', 'Shortlink berhasil disalin!');
            });

            document.getElementById('modalCopyBtn').addEventListener('click', function () {
                copyToClipboard('modalUrl', 'Shortlink berhasil disalin!');
            });

            document.getElementById('modalPasswordCopyBtn').addEventListener('click', function () {
                copyToClipboard('modalPassword', 'Password berhasil disalin!');
            });

            document.getElementById('saveAsPngBtn').addEventListener('click', saveQRCodeAsPNG);
        });

	    function copyToClipboard(elementId, successMessage) {
            var copyText = document.getElementById(elementId);
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            document.execCommand("copy");
            showSuccessModal(successMessage);
        }

        function isValidUrl(url) {
            var pattern = /^https?:\/\/.+/;
            return pattern.test(url);
        }

        function validateAlias(alias) {
            var invalidChars = alias.match(/[^a-z0-9~%.:_\-]/gi);
            return invalidChars ? invalidChars[0] : null;
        }

        function validateAliasLength(alias) {
            if (alias.trim() === "") return null;
            if (alias.length < 4) return "Customize URL minimal 4 karakter";
            if (alias.length > 75) return "Customize URL maksimal 75 karakter";
            return null;
        }

        function showCopyModal(shortenedUrl) {
            document.getElementById('copyTextInput').value = shortenedUrl;
            var copyModal = new bootstrap.Modal(document.getElementById('copyTextModal'));
            copyModal.show();

            document.getElementById('copyButton').onclick = function () {
                var copyText = document.getElementById('copyTextInput');
                copyText.select();
                document.execCommand('copy');

                alert('Shortlink berhasil disalin');
            };
        }

	function deleteLink(linkId) {
    document.getElementById('deleteConfirmModal').setAttribute('data-link-id', linkId);
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    deleteModal.show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    var modal = document.getElementById('deleteConfirmModal');
    var linkId = modal.getAttribute('data-link-id');
    var deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteConfirmModal'));

    fetch(`/shortener/delete/${linkId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            deleteModal.hide();
            setTimeout(() => {
                window.location.reload();
            }, 300);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorModal("Terjadi kesalahan saat menghapus link");
        deleteModal.hide();
    });
});

        function showPasswordModal(password) {
            document.getElementById('passwordInput').value = password;
            var passwordModal = new bootstrap.Modal(document.getElementById('passwordModal'));
            passwordModal.show();

            document.getElementById('copyPasswordButton').onclick = function () {
                var passwordInput = document.getElementById('passwordInput');
                passwordInput.select();
                document.execCommand('copy');

                alert('Password berhasil disalin');
            };
        }

        document.addEventListener('DOMContentLoaded', function () {
            function updateCountdown() {
                const countdownElements = document.querySelectorAll('.countdown');
                const daysElements = document.querySelectorAll('.expires-days');

                function processElement(el, isCountdown) {
                    const expiryDate = el.getAttribute('data-expiry');
                    const expiryType = el.getAttribute('data-expiry-type');

                    if (expiryType === 'Tanpa Batasan Periode Waktu') {
                        el.textContent = '-';
                        return;
                    }

                    if (!expiryDate) {
                        el.textContent = 'Never';
                        return;
                    }

                    const now = new Date().getTime();
                    const distance = new Date(expiryDate).getTime() - now;

                    if (distance < 0) {
                        el.textContent = 'Expired';
                        return;
                    }

                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    if (isCountdown) {
                        let countdownText = '';
                        if (days > 0) countdownText += `${days} hari `;
                        if (hours > 0) countdownText += `${hours} jam `;
                        if (minutes > 0) countdownText += `${minutes} menit `;
                        if (seconds > 0) countdownText += `${seconds} detik`;
                        el.textContent = countdownText + ' lagi';
                    } else {
                        if (days > 0) {
                            el.textContent = `${days} hari lagi`;
                        } else if (hours > 0) {
                            el.textContent = `${hours} jam lagi`;
                        } else {
                            el.textContent = `${minutes} menit lagi`;
                        }
                    }
                }

                countdownElements.forEach(el => processElement(el, true));
                daysElements.forEach(el => processElement(el, false));
            }

            updateCountdown();
            setInterval(updateCountdown, 1000);
        });

        function generateQRCode(options) {
            const {
                url,
                size = 200,
                logoPath = '/assets/images/logo/pu.jpg',
                logoSize = size / 5,
                canvasId,
                imageId
            } = options;

            const qrCodeUrl = `https://api.qrserver.com/v1/create-qr-code/?size=${size}x${size}&data=${encodeURIComponent(url)}`;

            const qrCodeImage = new Image();
            qrCodeImage.crossOrigin = 'Anonymous';
            qrCodeImage.src = qrCodeUrl;

            qrCodeImage.onload = function () {
                const canvas = canvasId ? document.getElementById(canvasId) : document.createElement('canvas');
                canvas.width = size;
                canvas.height = size;
                const ctx = canvas.getContext('2d');

                ctx.drawImage(qrCodeImage, 0, 0, size, size);

                const logo = new Image();
                logo.src = logoPath;
                logo.onload = function () {
                    const x = (size - logoSize) / 2;
                    const y = (size - logoSize) / 2;
                    ctx.drawImage(logo, x, y, logoSize, logoSize);

                    if (imageId) {
                        document.getElementById(imageId).src = canvas.toDataURL();
                    }

                    if (options.callback) {
                        options.callback(canvas.toDataURL());
                    }
                };
            };
        }

        function showQRCodeModal(originalUrl) {
            generateQRCode({
                url: originalUrl,
                size: 400,
                imageId: 'qrCodeImageInModal',
                callback: function (dataUrl) {
                    document.getElementById('downloadQRCode').href = dataUrl;

                    const qrCodeModal = new bootstrap.Modal(document.getElementById('qrCodeModal'));
                    qrCodeModal.show();
                }
            });

            document.getElementById('emailQRCode').onclick = function () {
                const emailSubject = "QR Code for " + originalUrl;
                const emailBody = "Cek QR Code berikut " + originalUrl;
                window.location.href = `mailto:?subject=${encodeURIComponent(emailSubject)}&body=${encodeURIComponent(emailBody)}`;
            };

            document.getElementById('whatsappQRCode').onclick = function () {
                const whatsappText = "Check QR Code berikut " + originalUrl;
                window.open("https://wa.me/?text=" + encodeURIComponent(whatsappText));
            };
        }

        function showEditModal(id, shortenedUrl, originalUrl, expiry) {
            document.getElementById('editLinkId').value = id;
            const parts = shortenedUrl.split('/');
            const encoded_userId = parts[3];
            const editablePart = parts[4];
            document.getElementById('editShortenedUrl').value = editablePart;
            document.getElementById('editEncodedUserId').value = encoded_userId;
            document.getElementById('editOriginalUrl').value = originalUrl;
            document.getElementById('editExpiry').value = expiry;
            
            new bootstrap.Modal(document.getElementById('editLinkModal')).show();
        }
        
        function updateLink() {
            const id = document.getElementById('editLinkId').value;
            const encoded_userId = document.getElementById('editEncodedUserId').value;
            const shortenedUrlSuffix = document.getElementById('editShortenedUrl').value;
            const originalUrl = document.getElementById('editOriginalUrl').value;
            const expiry = document.getElementById('editExpiry').value;
        
            if (shortenedUrlSuffix.length < 4) {
                showEditErrorModal("Shortlink minimal 4 karakter.");
                return;
            }
        
            if (shortenedUrlSuffix.length > 75) {
                showEditErrorModal("Shortlink maksimal 75 karakter.");
                return;
            }
        
            if (/\s/.test(shortenedUrlSuffix)) {
                showEditErrorModal("Customize URL tidak boleh mengandung spasi.");
                return;
            }
        
            if (!isValidUrl(originalUrl)) {
                showEditErrorModal("URL tidak valid");
                return;
            }
        
            showLoading();
        
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "/shortener/update", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4) {
                    hideLoading();
                    const editLinkModal = bootstrap.Modal.getInstance(document.getElementById('editLinkModal'));
                    if (editLinkModal) {
                        editLinkModal.hide();
                    }
        
                    if (xhr.status == 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            showEditSuccessModal("Shortlink berhasil diperbarui: " + response.shortened_url);
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        } else {
                            showEditErrorModal(response.error || "Gagal memperbarui Shortlink");
                        }
                    } else {
                        showEditErrorModal("Terjadi kesalahan. Mohon coba lagi.");
                    }
                }
            };
        
            const params = `id=${encodeURIComponent(id)}&encoded_userId=${encodeURIComponent(encoded_userId)}&shortened_url=${encodeURIComponent(shortenedUrlSuffix)}&original_url=${encodeURIComponent(originalUrl)}&expiry=${encodeURIComponent(expiry)}`;
    xhr.send(params);
        }
        
        function showEditSuccessModal(message) {
            document.getElementById('editSuccessMessage').textContent = message;
            const successModal = new bootstrap.Modal(document.getElementById('editSuccessModal'));
            successModal.show();
        }
        
        function showEditErrorModal(message) {
            document.getElementById('editErrorMessage').innerHTML = message;
            const errorModal = new bootstrap.Modal(document.getElementById('editErrorModal'));
            errorModal.show();
        }
        
        document.getElementById('downloadQRCode').addEventListener('click', function (e) {
            e.preventDefault();
            const imgSrc = document.getElementById('qrCodeImageInModal').src;
            fetch(imgSrc)
                .then(resp => resp.blob())
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    a.download = 'qr-code.png';
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                })
                .catch(() => alert('Terjadi kesalahan.'));
        });

        document.addEventListener('DOMContentLoaded', function () {
            const canvases = document.querySelectorAll('canvas[id^="qrCode_"]');
            canvases.forEach(canvas => {
                generateQRCode({
                    url: canvas.getAttribute('data-url'),
                    canvasId: canvas.id,
                    size: 200
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
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
        });

        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl, {
                    trigger: 'hover',
                    delay: { show: 0, hide: 100 }
                });
            });
        });

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
