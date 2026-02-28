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

    const galleryPreviewModal = document.getElementById('galleryPreviewModal');
    const galleryPreviewDialog = galleryPreviewModal?.querySelector('[data-gallery-preview-dialog]');
    const galleryPreviewImage = document.getElementById('galleryPreviewImage');
    const galleryPreviewDevice = document.getElementById('galleryPreviewDevice');
    const galleryPreviewLabel = document.getElementById('galleryPreviewLabel');
    const galleryPreviewCapturedAt = document.getElementById('galleryPreviewCapturedAt');
    const galleryPreviewClose = document.getElementById('galleryPreviewClose');

    function isGalleryPreviewOpen() {
        return galleryPreviewModal instanceof HTMLElement && !galleryPreviewModal.classList.contains('hidden');
    }

    function openGalleryPreview(payload) {
        if (!(galleryPreviewModal instanceof HTMLElement) || !(galleryPreviewDialog instanceof HTMLElement) ||
            !(galleryPreviewImage instanceof HTMLImageElement)) {
            return;
        }

        galleryPreviewImage.src = payload.src;
        galleryPreviewImage.alt = payload.alt || 'Preview galeri';
        if (galleryPreviewDevice instanceof HTMLElement) {
            galleryPreviewDevice.textContent = payload.device || '-';
        }
        if (galleryPreviewLabel instanceof HTMLElement) {
            const labelText = payload.score ? `${payload.label} (${payload.score}%)` : payload.label;
            galleryPreviewLabel.textContent = labelText || '-';
        }
        if (galleryPreviewCapturedAt instanceof HTMLElement) {
            galleryPreviewCapturedAt.textContent = payload.capturedAt || '-';
        }

        galleryPreviewModal.classList.remove('hidden');
        galleryPreviewModal.classList.add('flex');
        galleryPreviewModal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
        requestAnimationFrame(() => {
            galleryPreviewModal.classList.remove('opacity-0');
            galleryPreviewDialog.classList.remove('scale-95');
        });
    }

    function closeGalleryPreview() {
        if (!(galleryPreviewModal instanceof HTMLElement) || !(galleryPreviewDialog instanceof HTMLElement)) {
            return;
        }

        galleryPreviewModal.classList.add('opacity-0');
        galleryPreviewDialog.classList.add('scale-95');
        setTimeout(() => {
            galleryPreviewModal.classList.add('hidden');
            galleryPreviewModal.classList.remove('flex');
            galleryPreviewModal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
            if (galleryPreviewImage instanceof HTMLImageElement) {
                galleryPreviewImage.src = '';
            }
        }, 200);
    }

    const buildGalleryPreviewPayload = card => {
        if (!(card instanceof HTMLElement)) {
            return null;
        }

        const image = card.querySelector('img');
        if (!(image instanceof HTMLImageElement) || !image.src) {
            return null;
        }

        return {
            src: image.src,
            alt: image.alt || 'Preview galeri',
            device: card.dataset.galleryDevice || '-',
            label: card.dataset.galleryLabel || '-',
            score: card.dataset.galleryScore || '',
            capturedAt: card.dataset.galleryCapturedAt || '-',
        };
    };

    galleryPreviewClose?.addEventListener('click', closeGalleryPreview);

    galleryPreviewModal?.addEventListener('click', event => {
        if (event.target === galleryPreviewModal) {
            closeGalleryPreview();
        }
    });

    document.addEventListener('click', event => {
        const target = event.target;
        if (!(target instanceof Element)) {
            return;
        }

        const galleryCard = target.closest('[data-gallery-item]');
        if (!galleryCard) {
            return;
        }

        const payload = buildGalleryPreviewPayload(galleryCard);
        if (!payload) {
            return;
        }

        event.preventDefault();
        openGalleryPreview(payload);
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && isGalleryPreviewOpen()) {
            closeGalleryPreview();
            return;
        }

        if (event.key !== 'Enter' && event.key !== ' ') {
            return;
        }

        const activeElement = document.activeElement;
        if (!(activeElement instanceof HTMLElement) || !activeElement.matches('[data-gallery-item]')) {
            return;
        }

        const payload = buildGalleryPreviewPayload(activeElement);
        if (!payload) {
            return;
        }

        event.preventDefault();
        openGalleryPreview(payload);
    });

    (function initAdminRealtimeFilters() {
        const endpoint = "{{ route('admin.filters.panels') }}";
        let isFetching = false;
        const loadingBadge = document.getElementById('adminFilterLoadingBadge');

        const initDateModeForms = (root = document) => {
            const forms = Array.from(root.querySelectorAll('form[data-date-mode-form]'));
            forms.forEach(form => {
                const modeSelect = form.querySelector('[data-date-mode-select]');
                if (!(modeSelect instanceof HTMLSelectElement)) {
                    return;
                }

                const exactGroup = form.querySelector('[data-date-mode-group="exact"]');
                const rangeGroup = form.querySelector('[data-date-mode-group="range"]');

                const syncMode = () => {
                    const mode = modeSelect.value === 'exact' ? 'exact' : 'range';
                    if (exactGroup instanceof HTMLElement) {
                        exactGroup.classList.toggle('hidden', mode !== 'exact');
                        exactGroup.classList.toggle('flex', mode === 'exact');
                    }
                    if (rangeGroup instanceof HTMLElement) {
                        rangeGroup.classList.toggle('hidden', mode !== 'range');
                        rangeGroup.classList.toggle('flex', mode === 'range');
                    }
                };

                modeSelect.addEventListener('change', syncMode);
                syncMode();
            });
        };

        const getFilterForms = () => Array.from(document.querySelectorAll('form[data-admin-filter-form]'));

        const showGlobalLoadingBadge = () => {
            if (!(loadingBadge instanceof HTMLElement)) {
                return;
            }

            loadingBadge.classList.remove('hidden');
            loadingBadge.classList.add('inline-flex');
            loadingBadge.setAttribute('aria-hidden', 'false');
        };

        const hideGlobalLoadingBadge = () => {
            if (!(loadingBadge instanceof HTMLElement)) {
                return;
            }

            loadingBadge.classList.remove('inline-flex');
            loadingBadge.classList.add('hidden');
            loadingBadge.setAttribute('aria-hidden', 'true');
        };

        const toggleFilterControls = disabled => {
            getFilterForms().forEach(form => {
                form.classList.toggle('opacity-60', disabled);
                form.classList.toggle('cursor-not-allowed', disabled);

                Array.from(form.elements).forEach(field => {
                    if (!(field instanceof HTMLInputElement ||
                            field instanceof HTMLSelectElement ||
                            field instanceof HTMLTextAreaElement ||
                            field instanceof HTMLButtonElement)) {
                        return;
                    }

                    field.disabled = disabled;
                });
            });
        };

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

        const createDevicesSkeleton = () => {
            const rows = Array.from({
                length: 5
            }, () => `
                <tr class="border-t border-slate-100">
                    <td class="px-8 py-5"><div class="h-3 w-28 bg-slate-200 rounded animate-pulse"></div></td>
                    <td class="px-8 py-5"><div class="h-3 w-40 bg-slate-200 rounded animate-pulse"></div></td>
                    <td class="px-8 py-5"><div class="h-5 w-20 bg-slate-200 rounded-full animate-pulse"></div></td>
                    <td class="px-8 py-5 text-right"><div class="h-8 w-20 bg-slate-200 rounded-xl ml-auto animate-pulse"></div></td>
                </tr>
            `).join('');

            return `
                <section id="devices" class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-8 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                        <div class="space-y-2">
                            <div class="h-6 w-52 bg-slate-200 rounded animate-pulse"></div>
                            <div class="h-3 w-64 bg-slate-200 rounded animate-pulse"></div>
                        </div>
                        <div class="h-10 w-72 bg-slate-200 rounded-xl animate-pulse"></div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <tbody>${rows}</tbody>
                        </table>
                    </div>
                </section>
            `;
        };

        const createInferenceSkeleton = () => {
            const rows = Array.from({
                length: 6
            }, () => `
                <tr class="border-t border-slate-100">
                    <td class="px-8 py-5"><div class="h-3 w-36 bg-slate-200 rounded animate-pulse"></div></td>
                    <td class="px-8 py-5"><div class="h-3 w-24 bg-slate-200 rounded animate-pulse"></div></td>
                    <td class="px-8 py-5"><div class="h-5 w-24 bg-slate-200 rounded-full animate-pulse"></div></td>
                    <td class="px-8 py-5 text-center"><div class="h-3 w-20 bg-slate-200 rounded mx-auto animate-pulse"></div></td>
                    <td class="px-8 py-5 text-right"><div class="h-3 w-10 bg-slate-200 rounded ml-auto animate-pulse"></div></td>
                    <td class="px-8 py-5 text-right"><div class="h-8 w-16 bg-slate-200 rounded-lg ml-auto animate-pulse"></div></td>
                </tr>
            `).join('');

            return `
                <section id="inference" class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-8 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                        <div class="space-y-2">
                            <div class="h-6 w-56 bg-slate-200 rounded animate-pulse"></div>
                            <div class="h-3 w-72 bg-slate-200 rounded animate-pulse"></div>
                        </div>
                        <div class="h-10 w-80 bg-slate-200 rounded-xl animate-pulse"></div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <tbody>${rows}</tbody>
                        </table>
                    </div>
                </section>
            `;
        };

        const createGallerySkeleton = () => {
            const cards = Array.from({
                length: 10
            }, () => `
                <div class="aspect-square rounded-3xl bg-slate-200 animate-pulse"></div>
            `).join('');

            return `
                <section id="gallery">
                    <div class="mb-8 flex items-center justify-between gap-4">
                        <div class="space-y-2">
                            <div class="h-6 w-52 bg-slate-200 rounded animate-pulse"></div>
                            <div class="h-3 w-64 bg-slate-200 rounded animate-pulse"></div>
                        </div>
                        <div class="h-10 w-72 bg-slate-200 rounded-xl animate-pulse"></div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                        ${cards}
                    </div>
                </section>
            `;
        };

        const allAsyncSections = ['devices', 'inference', 'gallery'];

        const sectionByFormId = {
            adminDevicesFilterForm: 'devices',
            adminInferenceFilterForm: 'inference',
            adminGalleryFilterForm: 'gallery',
        };

        const capturePanelSnapshots = sectionIds => sectionIds.reduce((snapshots, sectionId) => {
            const section = document.getElementById(sectionId);
            if (section instanceof HTMLElement) {
                snapshots[sectionId] = section.outerHTML;
            }

            return snapshots;
        }, {});

        const restorePanelSnapshots = (snapshots, sectionIds) => {
            sectionIds.forEach(sectionId => {
                const html = snapshots[sectionId];
                if (!html) {
                    return;
                }

                replaceSection(sectionId, html);
            });
        };

        const renderLoadingPanels = sectionIds => {
            const skeletonRenderers = {
                devices: createDevicesSkeleton,
                inference: createInferenceSkeleton,
                gallery: createGallerySkeleton,
            };

            sectionIds.forEach(sectionId => {
                const renderer = skeletonRenderers[sectionId];
                if (typeof renderer !== 'function') {
                    return;
                }

                replaceSection(sectionId, renderer());
            });
        };

        const getLoadingSectionsFromForm = form => {
            const sectionId = sectionByFormId[form?.id];
            return sectionId ? [sectionId] : allAsyncSections;
        };

        const getLoadingSectionsFromQuery = query => {
            const sectionIds = [];
            if (query.has('inference_page')) {
                sectionIds.push('inference');
            }
            if (query.has('gallery_page')) {
                sectionIds.push('gallery');
            }

            return sectionIds.length > 0 ? sectionIds : allAsyncSections;
        };

        const applyFilterPanels = async ({
            extraParams = null,
            loadingSections = allAsyncSections,
        } = {}) => {
            if (isFetching) {
                return;
            }

            const sectionsToLoad = loadingSections.filter(sectionId => allAsyncSections.includes(sectionId));
            isFetching = true;
            const panelSnapshots = capturePanelSnapshots(sectionsToLoad);

            try {
                const params = collectAllFilterParams();
                if (extraParams instanceof URLSearchParams) {
                    extraParams.forEach((value, key) => {
                        params.set(key, value);
                    });
                }

                showGlobalLoadingBadge();
                toggleFilterControls(true);
                renderLoadingPanels(sectionsToLoad);

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
            } catch (error) {
                console.error(error);
                restorePanelSnapshots(panelSnapshots, sectionsToLoad);
            } finally {
                hideGlobalLoadingBadge();
                toggleFilterControls(false);
                isFetching = false;
                initDateModeForms();
                lucide.createIcons();
            }
        };

        document.addEventListener('submit', event => {
            const form = event.target;
            if (!(form instanceof HTMLFormElement) || !form.matches('form[data-admin-filter-form]')) {
                return;
            }

            event.preventDefault();
            applyFilterPanels({
                loadingSections: getLoadingSectionsFromForm(form),
            });
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

            const modeSelect = form.querySelector('[data-date-mode-select]');
            if (modeSelect instanceof HTMLSelectElement) {
                modeSelect.value = 'exact';
                modeSelect.dispatchEvent(new Event('change'));
            }

            applyFilterPanels({
                loadingSections: getLoadingSectionsFromForm(form),
            });
        });

        document.addEventListener('click', event => {
            const target = event.target;
            if (!(target instanceof Element)) {
                return;
            }

            const paginationLink = target.closest(
                '#inference .admin-pagination a, #gallery .admin-pagination a');
            if (!paginationLink) {
                return;
            }

            const href = paginationLink.getAttribute('href');
            if (!href) {
                return;
            }

            const url = new URL(href, window.location.origin);
            const query = url.searchParams;

            if (!query.has('inference_page') && !query.has('gallery_page')) {
                return;
            }

            event.preventDefault();
            applyFilterPanels({
                extraParams: query,
                loadingSections: getLoadingSectionsFromQuery(query),
            });
        });

        initDateModeForms();
    })();
</script>
