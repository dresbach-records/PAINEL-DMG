window.mailDashboard = function mailDashboard(config) {
    return {
        tab: 'overview',
        composing: false,
        sending: false,
        savingMailbox: false,
        loadingInbox: false,
        inboxMessages: [],
        mailboxes: config.mailboxes || [],
        selectedMailboxId: config.mailboxes.length ? config.mailboxes[0].id : null,
        form: {
            mailbox_id: config.mailboxes.length ? config.mailboxes[0].id : null,
            to: '',
            subject: '',
            template: 'nao-responder',
            priority: 'normal',
            queue: true,
            data: '{}',
        },
        mailboxSettings: {
            display_name: '',
            reply_to: '',
            avatar_url: '',
            institution_name: '',
            signature_html: '',
        },
        init() {
            this.syncMailboxSettings();
        },
        syncMailboxSettings() {
            const mailbox = this.mailboxes.find((item) => item.id === Number(this.selectedMailboxId));
            const profile = mailbox?.metadata?.sender_profile || {};

            this.form.mailbox_id = mailbox?.id || null;
            this.mailboxSettings.display_name = profile.display_name || mailbox?.name || '';
            this.mailboxSettings.reply_to = profile.reply_to || '';
            this.mailboxSettings.avatar_url = profile.avatar_url || '';
            this.mailboxSettings.institution_name = profile.institution_name || '';
            this.mailboxSettings.signature_html = profile.signature_html || '';
        },
        async saveMailboxSettings() {
            if (!this.selectedMailboxId) {
                return;
            }

            this.savingMailbox = true;

            try {
                const response = await fetch(`/mail/mailboxes/${this.selectedMailboxId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify({
                        sender_profile: this.mailboxSettings,
                    }),
                });

                const payload = await response.json();

                if (!response.ok) {
                    alert(payload.message || 'Falha ao salvar configurações da caixa.');
                    return;
                }

                this.mailboxes = this.mailboxes.map((item) => (item.id === payload.id ? payload : item));
                alert('Configurações da caixa salvas com sucesso.');
            } finally {
                this.savingMailbox = false;
            }
        },
        async sendMail() {
            this.sending = true;

            try {
                const response = await fetch('/mail/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify({
                        ...this.form,
                        mailbox_id: this.form.mailbox_id || this.selectedMailboxId,
                        data: this.safeJson(this.form.data),
                    }),
                });

                const payload = await response.json();

                if (!response.ok) {
                    alert(payload.message || 'Falha ao enviar e-mail.');
                    return;
                }

                alert(payload.message || 'Envio processado com sucesso.');
                this.composing = false;
            } finally {
                this.sending = false;
            }
        },
        async loadInbox() {
            if (!this.selectedMailboxId) {
                return;
            }

            this.loadingInbox = true;

            try {
                const response = await fetch(`/mail/mailboxes/${this.selectedMailboxId}/inbox`, {
                    headers: { Accept: 'application/json' },
                });

                const payload = await response.json();
                this.inboxMessages = payload.data || [];
            } finally {
                this.loadingInbox = false;
            }
        },
        safeJson(value) {
            try {
                return JSON.parse(value || '{}');
            } catch (_error) {
                return {};
            }
        },
        formatDate(value) {
            if (!value) {
                return '-';
            }

            return new Date(value).toLocaleString('pt-BR');
        },
    };
};
