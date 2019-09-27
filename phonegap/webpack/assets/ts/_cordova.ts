export default class Cordova {
    constructor() {
        document.addEventListener("deviceready", this.onDeviceReady, false);
    }

    onDeviceReady() {
        console.log('Device Ready');
        new Plugins.PushNotification();
    }
}
namespace Plugins {
    export class PushNotification {
        senderId = 596344214011;

        constructor() {
            // @ts-ignore
            const notifications = PushNotification.init({
                "android": {"senderID": this.senderId},
                "browser": {"pushServiceURL": "http://push.api.phonegap.com/v1/push"}
            });
            notifications.push.on('notification', function (data) {
                console.log(data);
                alert(data);
            });
            notifications.push.on('error', function (e) {
                console.log(e);
                alert(e);
            });
        }
    }
}