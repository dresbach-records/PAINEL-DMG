window.mailDashboard = function mailDashboard() {
    return {
        async reloadLogs() {
            await fetch('/mail/logs', {
                headers: {
                    Accept: 'application/json',
                },
            });
        },
    };
};
