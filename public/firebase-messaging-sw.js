importScripts('https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.12.0/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey: self.__FIREBASE_CONFIG__?.apiKey || '',
    authDomain: self.__FIREBASE_CONFIG__?.authDomain || '',
    projectId: self.__FIREBASE_CONFIG__?.projectId || 'on-track-1dae9',
    storageBucket: self.__FIREBASE_CONFIG__?.storageBucket || '',
    messagingSenderId: self.__FIREBASE_CONFIG__?.messagingSenderId || '',
    appId: self.__FIREBASE_CONFIG__?.appId || '',
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
    const title = payload.notification?.title || 'ON TRACK';
    const options = {
        body: payload.notification?.body || '',
        icon: '/images/brand/logo.png',
        badge: '/images/brand/logo.png',
        dir: 'rtl',
        data: payload.data || {},
        silent: false,
        vibrate: [200, 100, 200],
        requireInteraction: true,
    };

    self.registration.showNotification(title, options);
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    const url = event.notification.data?.url || '/';
    event.waitUntil(clients.openWindow(url));
});
