const elem = document.getElementById('instructions');
const instr = M.Modal.init(elem);
async function pnSubscribe() {
    if (pnAvailable()) {
        document.getElementById('process').innerHTML = '<i class="material-icons middled">hourglass_full</i> Waiting for your response, Please click on the <span class="bold-text ">Allow</span> button...<div class="progress"><div class="indeterminate"></div> </div>';
	  		      document.getElementById('status').innerHTML = '';
	  document.getElementById("push_btn").setAttribute("disabled","disabled");
        if (window.Notification.permission === "default") {
            await window.Notification.requestPermission();
        }
        if (Notification.permission === 'granted') {
            await pnRegisterSW();
        } else if (Notification.permission === 'denied') {
            document.getElementById('process').innerHTML = '<span class="red-text"><i class="material-icons middled">error</i> Looks like, you have denied us to send push notification! Please allow this site from your browser\'s nofification or site settings and reload this page.</span><br/><div class="center"><a href="#instructions" class="modal-trigger btn waves-effect waves-light orange">Instructions</a></div>';
		    instr.open();
        } else {
            document.getElementById('process').innerHTML = '<i class="material-icons middled">help</i> Looks like you forgot to click on the Allow button. Please click the button again and allow.';
	  		document.getElementById('status').innerHTML = '';
	  document.getElementById("push_btn").removeAttribute("disabled");

        }
    }
}
async function pnUnsubscribe() {
    var swReg = null;
    if (pnAvailable()) {
        await pnUnregisterSW();
    }
}
async function pnUpdate() {
    var swReg = null;
    if (pnAvailable()) {
        await pnUpdateSW();
    }
}
async function pnSubscribed() {
    var swReg = undefined;
    if (pnAvailable()) {
        swReg = await navigator.serviceWorker.getRegistration();
    }
    return (swReg != undefined);
}
function pnAvailable() {
    var bAvailable = false;
    if (window.isSecureContext) {
        bAvailable = (('serviceWorker' in navigator) && 
		              ('PushManager' in window) && 
					  ('Notification' in window)); 
    } else {
    }
    return bAvailable;
}
async function pnRegisterSW() {
    navigator.serviceWorker.register('notification-sw.js')
        .then((swReg) => {
            console.log('Registration succeeded. Scope is ' + swReg.scope);
            document.getElementById('push_btn').setAttribute('disabled','disabled');
            document.getElementById('status').innerHTML = '<i class="material-icons middled green-text">notifications_active</i> Notification Enabled';
var toastHTML = '<span><i class="material-icons middled green-text">check_circle</i> Push Notification Enabled!</span> <button class="btn-flat toast-action" onclick="M.Toast.dismissAll()">Close</button>';
M.toast({html: toastHTML});
            document.getElementById('process').innerHTML = '<span class="green-text"><i class="material-icons middled green-text">check_circle</i> Push notification is enabled sucessfully! Please wait a few seconds. If you are not redirected, please <a onclick="location.reload()">click here</a>.</span><div class="progress"><div class="indeterminate"></div> </div>';
            setTimeout("location.reload(true)",3000);
        }).catch((error) => {
        });
}
async function pnUnregisterSW() {
    navigator.serviceWorker.getRegistration()
        .then(function(reg) {
            reg.unregister()
                .then(function(bOK) {
                    if (bOK) {
                    } else {
                    }
                });
        });
}
async function pnUpdateSW() {
    navigator.serviceWorker.getRegistration()
        .then(function(reg) {
            reg.update()
                .then(function(bOK) {
                    if (bOK) {
                    } else {
                    }
                });
        });
}
if(pnAvailable()) {
    if (window.Notification.permission === "denied") {
    document.getElementById('process').innerHTML = '<span class="red-text"><i class="material-icons middled">error</i> Unfortunately, you have denied us to send push notification! Please allow this site from your browser\'s nofification or site settings and reload this page.</span><br/><div class="center"><a href="#instructions" class="modal-trigger btn waves-effect waves-light orange">Instructions</a></div>';
    document.getElementById('perm').innerHTML = '';
    document.getElementById('status').innerHTML = '<i class="material-icons red-text middled">notifications_off</i> Notifications are blocked!';
            } else {
                var strMsg;
                pnSubscribed()
                    .then(function(subscribed) {
                        if (subscribed) {
    document.getElementById('process').innerHTML = '<span class="green-text"><i class="material-icons middled">check_circle</i> Congratulation! You have already allowed us to send push notification! Thanks for subscribing.</span>';
    document.getElementById('perm').innerHTML = '';
    document.getElementById('status').innerHTML = '<i class="material-icons green-text middled">notifications_active</i> Notifications are Enabled';
                        } else {
   document.getElementById('status').innerHTML = '<i class="material-icons middled">notifications_active</i> Notifications are not enabled yet';
	document.getElementById("push_btn").removeAttribute("disabled");
	document.getElementById('process').innerHTML = '<i class="material-icons middled">help</i> Click the button to give the permission';
                        }       
                    });
            }
} else {
    document.getElementById('process').innerHTML = '<span class="red-text"><i class="material-icons middled">error</i> Sorry, your browser doesn\'t Push Notification! Consider upgrading your browser to latest version or change your browser to modern one.</span>';
    document.getElementById('perm').innerHTML = '';
        document.getElementById('status').innerHTML = '';
}