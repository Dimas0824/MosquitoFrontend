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

    document.querySelectorAll('form.confirm-delete').forEach(form => {
        form.addEventListener('submit', event => {
            event.preventDefault();
            openDeletionModal(form);
        });
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
</script>
