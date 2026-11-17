/* LAB BOOKING — interaksi antarmuka */
(function () {
    'use strict';

    var body = document.body;

    function onReady(callback) {
        if (document.readyState !== 'loading') {
            callback();
        } else {
            document.addEventListener('DOMContentLoaded', callback);
        }
    }

    onReady(function () {
        initSidebar();
        initDropdowns();
        initConfirmations();
        initFlashes();
        initPasswordToggles();
        initDemoAccounts();
        initAutoSubmit();
        initSubmitOnce();
        initPrintButtons();
        initCopyButtons();
        initBookingForm();
    });

    /* ---------------------------------------------------------------------
       Sidebar
    --------------------------------------------------------------------- */
    function initSidebar() {
        var toggle = document.getElementById('sidebarToggle');
        var close = document.getElementById('sidebarClose');
        var overlay = document.getElementById('sidebarOverlay');

        function openSidebar() {
            body.classList.add('sidebar-open');
            if (toggle) {
                toggle.setAttribute('aria-expanded', 'true');
            }
            var firstLink = document.querySelector('#sidebar .app-nav-link');
            if (firstLink) {
                window.setTimeout(function () { firstLink.focus(); }, 120);
            }
        }

        function closeSidebar() {
            body.classList.remove('sidebar-open');
            if (toggle) {
                toggle.setAttribute('aria-expanded', 'false');
            }
        }

        if (toggle) {
            toggle.addEventListener('click', function () {
                if (body.classList.contains('sidebar-open')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });
        }

        if (close) {
            close.addEventListener('click', closeSidebar);
        }

        if (overlay) {
            overlay.addEventListener('click', closeSidebar);
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && body.classList.contains('sidebar-open')) {
                closeSidebar();
                if (toggle) {
                    toggle.focus();
                }
            }
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth > 1080) {
                closeSidebar();
            }
        });
    }

    /* ---------------------------------------------------------------------
       Dropdown profil
    --------------------------------------------------------------------- */
    function closeAllDropdowns(except) {
        document.querySelectorAll('[data-dropdown]').forEach(function (dropdown) {
            if (dropdown === except) {
                return;
            }
            var toggle = dropdown.querySelector('[data-dropdown-toggle]');
            var menu = dropdown.querySelector('[data-dropdown-menu]');
            if (toggle) {
                toggle.setAttribute('aria-expanded', 'false');
            }
            if (menu) {
                menu.hidden = true;
            }
        });
    }

    function initDropdowns() {
        var dropdowns = document.querySelectorAll('[data-dropdown]');

        dropdowns.forEach(function (dropdown) {
            var toggle = dropdown.querySelector('[data-dropdown-toggle]');
            var menu = dropdown.querySelector('[data-dropdown-menu]');

            if (!toggle || !menu) {
                return;
            }

            toggle.addEventListener('click', function (event) {
                event.stopPropagation();
                var isOpen = toggle.getAttribute('aria-expanded') === 'true';
                closeAllDropdowns(dropdown);
                toggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
                menu.hidden = isOpen;
            });

            menu.addEventListener('click', function (event) {
                event.stopPropagation();
            });
        });

        document.addEventListener('click', function () {
            closeAllDropdowns();
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeAllDropdowns();
            }
        });
    }

    /* ---------------------------------------------------------------------
       Konfirmasi tindakan
    --------------------------------------------------------------------- */
    function initConfirmations() {
        document.querySelectorAll('form[data-confirm]').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                var message = form.getAttribute('data-confirm') || 'Apakah Anda yakin ingin melanjutkan?';
                if (!window.confirm(message)) {
                    event.preventDefault();
                }
            });
        });

        document.querySelectorAll('[data-confirm-click]').forEach(function (element) {
            element.addEventListener('click', function (event) {
                var message = element.getAttribute('data-confirm-click') || 'Apakah Anda yakin?';
                if (!window.confirm(message)) {
                    event.preventDefault();
                }
            });
        });
    }

    /* ---------------------------------------------------------------------
       Notifikasi flash
    --------------------------------------------------------------------- */
    function dismissFlash(element) {
        if (!element || element.classList.contains('is-hiding')) {
            return;
        }
        element.classList.add('is-hiding');
        window.setTimeout(function () {
            element.remove();
        }, 260);
    }

    function initFlashes() {
        document.querySelectorAll('.flash').forEach(function (flash) {
            var close = flash.querySelector('[data-dismiss-flash]');

            if (close) {
                close.addEventListener('click', function () {
                    dismissFlash(flash);
                });
            }

            window.setTimeout(function () {
                dismissFlash(flash);
            }, 6500);
        });
    }

    /* ---------------------------------------------------------------------
       Tampilkan / sembunyikan password
    --------------------------------------------------------------------- */
    function initPasswordToggles() {
        document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
            var inputId = button.getAttribute('data-toggle-password');
            var input = document.getElementById(inputId);

            if (!input) {
                return;
            }

            button.addEventListener('click', function () {
                var isVisible = input.type === 'text';
                input.type = isVisible ? 'password' : 'text';
                button.classList.toggle('is-visible', !isVisible);
                button.setAttribute('aria-label', isVisible ? 'Tampilkan password' : 'Sembunyikan password');
                button.setAttribute('aria-pressed', isVisible ? 'false' : 'true');
            });
        });
    }

    /* ---------------------------------------------------------------------
       Akun demo pada halaman login
    --------------------------------------------------------------------- */
    function initDemoAccounts() {
        document.querySelectorAll('[data-demo-email]').forEach(function (button) {
            button.addEventListener('click', function () {
                var email = document.getElementById('email');
                var password = document.getElementById('password');

                if (email) {
                    email.value = button.getAttribute('data-demo-email') || '';
                }
                if (password) {
                    password.value = button.getAttribute('data-demo-password') || '';
                }
                if (email) {
                    email.focus();
                }
            });
        });
    }

    /* ---------------------------------------------------------------------
       Form filter otomatis
    --------------------------------------------------------------------- */
    function initAutoSubmit() {
        document.querySelectorAll('[data-auto-submit]').forEach(function (element) {
            element.addEventListener('change', function () {
                var form = element.closest('form');
                if (form) {
                    form.submit();
                }
            });
        });
    }

    /* ---------------------------------------------------------------------
       Cegah pengiriman ganda
    --------------------------------------------------------------------- */
    function initSubmitOnce() {
        document.querySelectorAll('form[data-submit-once]').forEach(function (form) {
            form.addEventListener('submit', function () {
                if (form.checkValidity && !form.checkValidity()) {
                    return;
                }
                var submit = form.querySelector('button[type="submit"]');
                if (submit) {
                    window.setTimeout(function () {
                        submit.disabled = true;
                        submit.classList.add('is-disabled');
                    }, 10);
                }
            });
        });
    }

    function initPrintButtons() {
        document.querySelectorAll('[data-print]').forEach(function (button) {
            button.addEventListener('click', function () {
                window.print();
            });
        });
    }

    /* ---------------------------------------------------------------------
       Salin teks
    --------------------------------------------------------------------- */
    function initCopyButtons() {
        document.querySelectorAll('[data-copy]').forEach(function (button) {
            button.addEventListener('click', function () {
                var value = button.getAttribute('data-copy') || '';
                var original = button.getAttribute('title') || 'Salin';

                function done() {
                    button.setAttribute('title', 'Tersalin');
                    button.classList.add('is-copied');
                    window.setTimeout(function () {
                        button.setAttribute('title', original);
                        button.classList.remove('is-copied');
                    }, 1600);
                }

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(value).then(done).catch(function () {});
                } else {
                    var input = document.createElement('input');
                    input.value = value;
                    document.body.appendChild(input);
                    input.select();
                    try { document.execCommand('copy'); done(); } catch (error) {}
                    input.remove();
                }
            });
        });
    }

    /* ---------------------------------------------------------------------
       Form booking: ringkasan lab & ketersediaan jadwal
    --------------------------------------------------------------------- */
    function initBookingForm() {
        var form = document.getElementById('booking-form');

        if (!form) {
            return;
        }

        var labSelect = document.getElementById('lab_id');
        var dateInput = document.getElementById('booking_date');
        var startSelect = document.getElementById('start_time');
        var endSelect = document.getElementById('end_time');
        var list = document.getElementById('availabilityList');
        var emptyText = document.getElementById('availabilityEmpty');
        var conflictNote = document.getElementById('conflictNote');
        var slotsUrl = form.getAttribute('data-slots-url');
        var bookings = [];
        var loadedDate = '';
        var requestCounter = 0;

        var summaryName = document.getElementById('summaryLabName');
        var summaryMeta = document.getElementById('summaryLabMeta');
        var summaryCode = document.getElementById('summaryLabCode');
        var summaryCapacity = document.getElementById('summaryCapacity');
        var summaryLocation = document.getElementById('summaryLocation');

        function timeToMinutes(value) {
            if (!value || value.indexOf(':') === -1) {
                return 0;
            }
            var parts = value.split(':');
            return parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10);
        }

        function overlaps(startA, endA, startB, endB) {
            return timeToMinutes(startA) < timeToMinutes(endB) && timeToMinutes(endA) > timeToMinutes(startB);
        }

        function updateSummary() {
            if (!labSelect) {
                return;
            }

            var option = labSelect.options[labSelect.selectedIndex];
            if (!option) {
                return;
            }

            if (summaryName) {
                summaryName.textContent = option.getAttribute('data-name') || option.textContent;
            }
            if (summaryMeta) {
                summaryMeta.textContent = (option.getAttribute('data-code') || '') + ' · ' + (option.getAttribute('data-location') || '');
            }
            if (summaryCode) {
                summaryCode.textContent = option.getAttribute('data-code') || '-';
            }
            if (summaryCapacity) {
                summaryCapacity.textContent = (option.getAttribute('data-capacity') || '-') + ' orang';
            }
            if (summaryLocation) {
                summaryLocation.textContent = option.getAttribute('data-location') || '-';
            }
        }

        function checkConflict() {
            if (!conflictNote || !startSelect || !endSelect || !labSelect) {
                return;
            }

            var start = startSelect.value;
            var end = endSelect.value;
            var labId = parseInt(labSelect.value, 10);
            var hasConflict = false;

            if (start && end && timeToMinutes(start) < timeToMinutes(end)) {
                hasConflict = bookings.some(function (booking) {
                    return parseInt(booking.lab_id, 10) === labId
                        && overlaps(start, end, booking.start_time, booking.end_time);
                });
            }

            conflictNote.classList.toggle('is-visible', hasConflict);
        }

        function renderSlots() {
            if (!list) {
                return;
            }

            var labId = labSelect ? parseInt(labSelect.value, 10) : 0;
            var filtered = bookings.filter(function (booking) {
                return parseInt(booking.lab_id, 10) === labId;
            });

            list.innerHTML = '';

            if (emptyText) {
                emptyText.style.display = filtered.length === 0 ? 'block' : 'none';
            }

            filtered.forEach(function (booking) {
                var item = document.createElement('div');
                item.className = 'slot-item ' + (booking.status === 'approved' ? 'is-approved' : 'is-pending');

                var range = document.createElement('strong');
                range.textContent = booking.start_time + ' – ' + booking.end_time;

                var status = document.createElement('span');
                status.className = 'badge badge-' + (booking.status === 'approved' ? 'success' : 'warning');
                status.textContent = booking.status === 'approved' ? 'Disetujui' : 'Menunggu';

                item.appendChild(range);
                item.appendChild(status);
                list.appendChild(item);
            });

            checkConflict();
        }

        function loadSlots() {
            if (!dateInput || !dateInput.value || !slotsUrl) {
                return;
            }

            if (loadedDate === dateInput.value && bookings.length > 0) {
                renderSlots();
                return;
            }

            var requestId = ++requestCounter;
            loadedDate = dateInput.value;

            if (list) {
                list.innerHTML = '<div class="slot-item"><span>Memuat jadwal…</span></div>';
            }
            if (emptyText) {
                emptyText.style.display = 'none';
            }

            var separator = slotsUrl.indexOf('?') === -1 ? '?' : '&';
            fetch(slotsUrl + separator + 'date=' + encodeURIComponent(dateInput.value), {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin'
            })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('Gagal memuat jadwal');
                    }
                    return response.json();
                })
                .then(function (data) {
                    if (requestId !== requestCounter) {
                        return;
                    }
                    bookings = Array.isArray(data.bookings) ? data.bookings : [];
                    renderSlots();
                })
                .catch(function () {
                    if (requestId !== requestCounter) {
                        return;
                    }
                    bookings = [];
                    if (list) {
                        list.innerHTML = '<div class="slot-item"><span>Jadwal tidak dapat dimuat.</span></div>';
                    }
                });
        }

        if (labSelect) {
            labSelect.addEventListener('change', function () {
                updateSummary();
                renderSlots();
            });
            updateSummary();
        }

        if (dateInput) {
            dateInput.addEventListener('change', loadSlots);
        }

        if (startSelect && endSelect) {
            startSelect.addEventListener('change', function () {
                var startMinutes = timeToMinutes(startSelect.value);
                var endMinutes = timeToMinutes(endSelect.value);

                if (!endSelect.value || endMinutes <= startMinutes) {
                    var target = startMinutes + 60;
                    var available = Array.prototype.slice.call(endSelect.options).some(function (option) {
                        return timeToMinutes(option.value) === target;
                    });
                    if (available) {
                        endSelect.value = String(Math.floor(target / 60)).padStart(2, '0') + ':' + String(target % 60).padStart(2, '0');
                    }
                }
                checkConflict();
            });
            endSelect.addEventListener('change', checkConflict);
        }

        loadSlots();
    }
})();
