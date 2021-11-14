const strAppPublicKey  = 'BIZk9xoCMdDzU9KjhRs8Pji8kodhBz9HjYWsDTH13eTOljnT0EjiwZn2vf_8ceuxzCgBmCLg6NDSNr8w4_YSnWs';
const strSubscriberURL = 'ajax';
const strDefTitle      = 'AtoZFlix';
const strDefIcon       = 'https://atozflix.ml/assets/images/badge.png';
 function encodeToUint8Array(strBase64) {
    var strPadding = '='.repeat((4 - (strBase64.length % 4)) % 4);
    var strBase64 = (strBase64 + strPadding).replace(/\-/g, '+').replace(/_/g, '/');
    var rawData = atob(strBase64);
    var aOutput = new Uint8Array(rawData.length);
    for (i = 0; i < rawData.length; ++i) {
        aOutput[i] = rawData.charCodeAt(i);
    }
    return aOutput;
}
async function pnSubscribe(event) {
    console.log('Serviceworker: activate event');
    try {
        var appPublicKey = encodeToUint8Array(strAppPublicKey);
        var opt = {
                applicationServerKey: appPublicKey, 
                userVisibleOnly: true
            };
        
        self.registration.pushManager.subscribe(opt)
            .then((sub) => {
                pnSaveSubscription(sub)
                    .then((response) => {
                        console.log(response);
                    }).catch((e) => {
                        console.log('SaveSubscription failed with: ' + e);
                    });
            }, ).catch((e) => {
                console.log('Subscription failed with: ' + e);
            });
        
    } catch (e) {
        console.log('Error subscribing notifications: ' + e);
    }
}
async function pnSubscriptionChange(event) {
    console.log('Serviceworker: subscription change event: ' + event);
    try {
        self.registration.pushManager.subscribe(event.oldSubscription.options)
            .then((sub) => {
                pnSaveSubscription(sub)
                    .then((response) => {
                        console.log(response);
                    }).catch((e) => {
                        console.log('SaveSubscription failed with: ' + e);
                    });
            }, ).catch((e) => {
                console.log('Subscription failed with: ' + e);
            });
        
    } catch (e) {
        console.log('Error subscribing notifications: ' + e);
    }
}
async function pnSaveSubscription(sub) {
    var body = JSON.parse(JSON.stringify(sub));
    body.userAgent = navigator.userAgent;
    var fetchdata = {
            method: 'post',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body),
          };
          var response = await fetch(strSubscriberURL, fetchdata);
          return response.text();
}
function pnPushNotification(event) {
    console.log('push event: ' + event);
    var strTitle = strDefTitle;
    var oPayload = null;
    var opt = { icon: strDefIcon };
    if (event.data) {
        try {
            oPayload = JSON.parse(event.data.text());
        } catch (e) {
            opt = {
                icon: strDefIcon,
                body: event.data.text(),
            };
        }
        if (oPayload) {
            if (oPayload.title != undefined && oPayload.title != '') {
                strTitle = oPayload.title;
            }
            opt = oPayload.opt;
            if (oPayload.opt.icon == undefined || 
                oPayload.opt.icon == null || 
                oPayload.icon == '') {
               opt.icon = strDefIcon;
            }
        }
    }
    var promise = self.registration.showNotification(strTitle, opt);
    event.waitUntil(promise);
}
function pnNotificationClick(event) {
    console.log('notificationclick event: ' + event);
    if (event.notification.data && event.notification.data.url) {
        const promise = clients.openWindow(event.notification.data.url);
        event.waitUntil(promise);
    }
    if (event.action != "") {
        console.log('notificationclick action: ' + event.action);
    }
}
function pnNotificationClose(event) {
    console.log('notificationclose event: ' + event);
}
self.addEventListener('activate', pnSubscribe);
self.addEventListener('push', pnPushNotification);
self.addEventListener('notificationclick', pnNotificationClick);
self.addEventListener('pushsubscriptionchange', pnSubscriptionChange);
self.addEventListener('notificationclose', pnNotificationClose); 