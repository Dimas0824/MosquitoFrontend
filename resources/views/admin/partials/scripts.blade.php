<script>
    lucide.createIcons();

    function openModal(id) {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.querySelector('div').classList.remove('scale-95');
        }, 10);
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.add('opacity-0');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    function openDeviceModal(dataset = null) {
        const form = document.getElementById('deviceForm');
        const methodInput = document.getElementById('deviceFormMethod');
        const title = document.getElementById('deviceModalTitle');
        const submitBtn = document.getElementById('deviceSubmitButton');

        const code = document.getElementById('deviceCode');
        const passwordInput = document.getElementById('devicePassword');
        const locationInput = document.getElementById('deviceLocation');
        const descriptionInput = document.getElementById('deviceDescription');
        const activeInput = document.getElementById('deviceIsActive');

        if (dataset && dataset.deviceId) {
            form.action = dataset.updateAction;
            methodInput.value = 'PATCH';
            title.textContent = 'Edit Perangkat';
            submitBtn.textContent = 'Perbarui Perangkat';

            code.value = dataset.deviceCode || '';
            passwordInput.value = '';
            passwordInput.required = false;
            locationInput.value = dataset.deviceLocation || '';
            descriptionInput.value = dataset.deviceDescription || '';
            activeInput.checked = dataset.deviceActive === '1';
        } else {
            form.action = form.getAttribute('action');
            methodInput.value = 'POST';
            title.textContent = 'Tambah Perangkat';
            submitBtn.textContent = 'Simpan Perangkat';

            code.value = '';
            passwordInput.value = '';
            passwordInput.required = true;
            locationInput.value = '';
            descriptionInput.value = '';
            activeInput.checked = true;
        }

        openModal('modalDevice');
    }

    const deleteModal = document.getElementById('confirmDeletionModal');
    const deleteModalInner = deleteModal?.querySelector('div');
    const confirmDeleteButton = document.getElementById('confirmDeletionButton');
    let pendingDeleteForm = null;

    function openDeletionModal(form) {
        if (!deleteModal || !deleteModalInner) return;
        pendingDeleteForm = form;
        const deviceCode = form.dataset.deviceCode || 'perangkat ini';
        const message = document.getElementById('confirmDeletionMessage');
        message.textContent = `Hapus ${deviceCode}? Tindakan ini tidak dapat dibatalkan.`;
        deleteModal.classList.remove('hidden');
        deleteModal.classList.add('flex');
        setTimeout(() => {
            deleteModalInner.classList.remove('scale-95');
            deleteModalInner.classList.remove('opacity-0');
        }, 10);
    }

    function closeDeletionModal() {
        if (!deleteModal || !deleteModalInner) return;
        deleteModalInner.classList.add('scale-95');
        deleteModalInner.classList.add('opacity-0');
        setTimeout(() => {
            deleteModal.classList.add('hidden');
            deleteModal.classList.remove('flex');
            pendingDeleteForm = null;
        }, 200);
    }

    confirmDeleteButton?.addEventListener('click', () => {
        if (!pendingDeleteForm) return;
        closeDeletionModal();
        pendingDeleteForm.submit();
    });

    deleteModal?.addEventListener('click', event => {
        if (event.target === deleteModal) {
            closeDeletionModal();
        }
    });

    document.addEventListener('submit', event => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        if (form.matches('form.confirm-delete')) {
            event.preventDefault();
            openDeletionModal(form);
        }
    });

    document.querySelectorAll('nav a').forEach(link => {
        link.addEventListener('click', function() {
            document.querySelectorAll('nav a').forEach(l => l.classList.remove('sidebar-item-active',
                'text-indigo-600'));
            document.querySelectorAll('nav a').forEach(l => l.classList.add('text-slate-500'));
            this.classList.add('sidebar-item-active');
            this.classList.remove('text-slate-500');
        });
    });

    window.onclick = function(event) {
        if (event.target && event.target.id === 'modalDevice') {
            closeModal('modalDevice');
        }
    }

    const inferenceEditModal = document.getElementById('inferenceEditModal');
    const inferenceEditForm = document.getElementById('inferenceEditForm');
    const inferenceDevice = document.getElementById('inferenceDevice');
    const inferenceStatus = document.getElementById('inferenceStatus');
    const inferenceTotalJentik = document.getElementById('inferenceTotalJentik');
    const inferenceScore = document.getElementById('inferenceScore');
    const inferenceEditSubtitle = document.getElementById('inferenceEditSubtitle');
    const inferenceEditTitle = document.getElementById('inferenceEditTitle');
    const inferenceRouteTemplate = "{{ route('admin.inference.update', ['inference' => '__ID__']) }}";

    function openInferenceEditModal(data) {
        if (!inferenceEditModal || !inferenceEditForm) return;
        const action = inferenceRouteTemplate.replace('__ID__', data.id);
        inferenceEditForm.action = action;

        inferenceDevice.value = data.device_code || '-';
        inferenceStatus.value = data.label || '';
        inferenceTotalJentik.value = data.total_jentik || 0;
        inferenceScore.value = data.score ? Number(data.score) * 100 : '';
        inferenceEditSubtitle.textContent = data.timestamp ? `Timestamp: ${data.timestamp}` : '';
        inferenceEditTitle.textContent = 'Perbarui Inferensi';

        inferenceEditModal.classList.remove('hidden');
        inferenceEditModal.classList.add('flex');
        setTimeout(() => {
            inferenceEditModal.classList.remove('opacity-0');
            inferenceEditModal.querySelector('div').classList.remove('scale-95');
        }, 10);
    }

    function closeInferenceEditModal() {
        if (!inferenceEditModal) return;
        inferenceEditModal.classList.add('opacity-0');
        inferenceEditModal.querySelector('div').classList.add('scale-95');
        setTimeout(() => {
            inferenceEditModal.classList.add('hidden');
            inferenceEditModal.classList.remove('flex');
        }, 200);
    }

    inferenceEditModal?.addEventListener('click', event => {
        if (event.target === inferenceEditModal) {
            closeInferenceEditModal();
        }
    });

    (function initAdminRealtimeFilters() {
        const endpoint = "{{ route('admin.filters.panels') }}";
        let isFetching = false;

        const getFilterForms = () => Array.from(document.querySelectorAll('form[data-admin-filter-form]'));

        const setFieldValue = element => {
            if (element instanceof HTMLSelectElement) {
                const firstOption = element.options[0];
                element.value = firstOption ? firstOption.value : '';
                return;
            }

            if (element instanceof HTMLInputElement) {
                if (element.type === 'checkbox' || element.type === 'radio') {
                    element.checked = false;
                    return;
                }

                element.value = '';
            }
        };

        const collectAllFilterParams = () => {
            const params = new URLSearchParams();
            getFilterForms().forEach(form => {
                Array.from(form.elements).forEach(field => {
                    if (!(field instanceof HTMLInputElement ||
                            field instanceof HTMLSelectElement ||
                            field instanceof HTMLTextAreaElement)) {
                        return;
                    }

                    if (!field.name || field.disabled) {
                        return;
                    }

                    if ((field instanceof HTMLInputElement) && (field.type === 'checkbox' ||
                            field.type === 'radio') && !field.checked) {
                        return;
                    }

                    params.set(field.name, field.value ?? '');
                });
            });

            return params;
        };

        const replaceSection = (sectionId, html) => {
            if (!html) {
                return;
            }

            const current = document.getElementById(sectionId);
            if (!current) {
                return;
            }

            const template = document.createElement('template');
            template.innerHTML = html.trim();
            const next = template.content.firstElementChild;
            if (!next) {
                return;
            }

            current.replaceWith(next);
        };

        const applyFilterPanels = async () => {
            if (isFetching) {
                return;
            }

            isFetching = true;

            try {
                const params = collectAllFilterParams();
                const response = await fetch(`${endpoint}?${params.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) {
                    throw new Error('Gagal mengambil data filter admin');
                }

                const payload = await response.json();
                replaceSection('devices', payload.devices_html);
                replaceSection('inference', payload.inference_html);
                replaceSection('gallery', payload.gallery_html);
                lucide.createIcons();
            } catch (error) {
                console.error(error);
            } finally {
                isFetching = false;
            }
        };

        document.addEventListener('submit', event => {
            const form = event.target;
            if (!(form instanceof HTMLFormElement) || !form.matches('form[data-admin-filter-form]')) {
                return;
            }

            event.preventDefault();
            applyFilterPanels();
        });

        document.addEventListener('click', event => {
            const target = event.target;
            if (!(target instanceof Element)) {
                return;
            }

            const resetButton = target.closest('.admin-filter-reset');
            if (!resetButton) {
                return;
            }

            const form = resetButton.closest('form[data-admin-filter-form]');
            if (!form) {
                return;
            }

            event.preventDefault();

            Array.from(form.elements).forEach(field => {
                if (!(field instanceof HTMLInputElement || field instanceof HTMLSelectElement ||
                        field instanceof HTMLTextAreaElement)) {
                    return;
                }

                if (!field.name || field.disabled) {
                    return;
                }

                setFieldValue(field);
            });

            applyFilterPanels();
        });
    })();

    (function initAdminChart() {
        const canvas = document.getElementById('adminDetectionsChart');
        const filterForm = document.getElementById('adminChartFilterForm');
        if (!canvas || !filterForm || typeof Chart === 'undefined') {
            return;
        }

        const modeInput = document.getElementById('chartMode');
        const dateRangeFields = document.getElementById('dateRangeFields');
        const weekFields = document.getElementById('weekFields');
        const dateFromInput = document.getElementById('chartDateFrom');
        const dateToInput = document.getElementById('chartDateTo');
        const weekInput = document.getElementById('chartWeek');
        const monthInput = document.getElementById('chartMonth');
        const yearInput = document.getElementById('chartYear');
        const resetButton = document.getElementById('adminChartReset');
        const titleEl = document.getElementById('adminChartTitle');
        const rangeTextEl = document.getElementById('adminChartRangeText');
        const totalEl = document.getElementById('adminChartTotal');
        const averageEl = document.getElementById('adminChartAverage');
        const endpoint = "{{ route('admin.chart.data') }}";

        const today = new Date();
        const toDateInputString = date => date.toISOString().slice(0, 10);
        const defaultDateTo = toDateInputString(today);
        const defaultDateFromDate = new Date(today);
        defaultDateFromDate.setDate(defaultDateFromDate.getDate() - 6);
        const defaultDateFrom = toDateInputString(defaultDateFromDate);

        if (!dateFromInput.value) {
            dateFromInput.value = defaultDateFrom;
        }

        if (!dateToInput.value) {
            dateToInput.value = defaultDateTo;
        }

        const ctx = canvas.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.32)');
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0.02)');

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Total Jentik',
                    data: [],
                    borderColor: 'rgba(79, 70, 229, 0.95)',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.38,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: 'rgba(79, 70, 229, 1)',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                animation: {
                    duration: 520,
                    easing: 'easeOutQuart',
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.92)',
                        titleColor: '#fff',
                        bodyColor: '#e2e8f0',
                        borderColor: 'rgba(99, 102, 241, 0.35)',
                        borderWidth: 1,
                        cornerRadius: 12,
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            label: context => `Total jentik: ${context.parsed.y ?? 0}`,
                        },
                    },
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false,
                        },
                        ticks: {
                            color: '#64748b',
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 8,
                            font: {
                                size: 11,
                                weight: '600',
                            },
                        },
                    },
                    y: {
                        beginAtZero: true,
                        border: {
                            display: false,
                        },
                        grid: {
                            color: 'rgba(148, 163, 184, 0.18)',
                            drawBorder: false,
                        },
                        ticks: {
                            precision: 0,
                            color: '#94a3b8',
                            font: {
                                size: 11,
                                weight: '600',
                            },
                        },
                    },
                },
            },
        });

        function syncModeFields() {
            const isWeekMode = modeInput.value === 'week_in_month';
            dateRangeFields.classList.toggle('hidden', isWeekMode);
            dateRangeFields.classList.toggle('flex', !isWeekMode);
            weekFields.classList.toggle('hidden', !isWeekMode);
            weekFields.classList.toggle('flex', isWeekMode);
        }

        function updateChart(data) {
            chart.data.labels = Array.isArray(data.labels) ? data.labels : [];
            chart.data.datasets[0].data = Array.isArray(data.values) ? data.values : [];
            chart.update();

            titleEl.textContent = data.meta?.title || 'Deteksi Jentik per Hari';
            rangeTextEl.textContent = data.meta?.range_text || '-';

            const values = Array.isArray(data.values) ? data.values.map(value => Number(value) || 0) : [];
            const total = values.reduce((sum, value) => sum + value, 0);
            const average = values.length > 0 ? (total / values.length) : 0;

            if (totalEl) {
                totalEl.textContent = String(total);
            }

            if (averageEl) {
                averageEl.textContent = average.toFixed(1);
            }

            if (data.meta?.weeks_in_month && weekInput) {
                const maxWeek = Number(data.meta.weeks_in_month) || 6;
                Array.from(weekInput.options).forEach(option => {
                    option.disabled = Number(option.value) > maxWeek;
                });
            }
        }

        async function fetchChartData() {
            const mode = modeInput.value;
            const params = new URLSearchParams({
                mode,
            });

            if (mode === 'week_in_month') {
                params.set('week', weekInput.value || '1');
                params.set('month', monthInput.value || String(today.getMonth() + 1));
                params.set('year', yearInput.value || String(today.getFullYear()));
            } else {
                params.set('date_from', dateFromInput.value || defaultDateFrom);
                params.set('date_to', dateToInput.value || defaultDateTo);
            }

            rangeTextEl.textContent = 'Memuat data...';

            try {
                const response = await fetch(`${endpoint}?${params.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!response.ok) {
                    throw new Error('Gagal mengambil data grafik');
                }

                const payload = await response.json();
                updateChart(payload);
            } catch (error) {
                rangeTextEl.textContent = 'Gagal memuat data';
            }
        }

        modeInput.addEventListener('change', () => {
            syncModeFields();
            fetchChartData();
        });

        filterForm.addEventListener('submit', event => {
            event.preventDefault();
            fetchChartData();
        });

        resetButton?.addEventListener('click', () => {
            modeInput.value = 'date_range';
            dateFromInput.value = defaultDateFrom;
            dateToInput.value = defaultDateTo;
            weekInput.value = '1';
            monthInput.value = String(today.getMonth() + 1);
            yearInput.value = String(today.getFullYear());
            syncModeFields();
            fetchChartData();
        });

        syncModeFields();
        fetchChartData();
    })();
</script>
