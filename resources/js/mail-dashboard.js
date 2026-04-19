window.mailDashboard = function mailDashboard() {
    return {
 codex/task-title-htqlyo
        mailboxEmail: '',
        inbox: [],
        async reloadLogs() {
            await fetch('/mail/logs', {
                headers: { Accept: 'application/json' },
            });
        },
        async loadInbox(mailboxId) {
            const response = await fetch(`/mail/mailboxes/${mailboxId}/inbox`, {
                headers: { Accept: 'application/json' },
            });

            const payload = await response.json();
            this.inbox = payload.data ?? [];
        },

        async reloadLogs() {
            await fetch('/mail/logs', {
                headers: {
                    Accept: 'application/json',
                },
            });
        },
 main
    };
};
