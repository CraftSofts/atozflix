if (window.isSecureContext) {
    if ('serviceWorker' in navigator) {
        if ('PushManager' in window) {
            if ('Notification' in window) {
                if (window.Notification.permission === "denied" || window.Notification.permission === "default") {
                    document.getElementById("push").style.display = "block";
                }
            }
        }
    }
}