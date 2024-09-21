if ("serviceWorker" in navigator) {
  navigator.serviceWorker
    .register("/firebase-messaging-sw.js")
    .then((registration) => {
      console.log("Service Worker registered with scope:", registration.scope);
    })
    .catch((err) => {
      console.log("Service Worker registration failed:", err);
    });
}

// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getAnalytics } from "firebase/analytics";
import { getMessaging, getToken, onMessage } from "firebase/messaging";

// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
  apiKey: "YOUR_API_KEY_HERE",
  authDomain: "YOUR_AUTH_DOMAIN_HERE",
  projectId: "YOUR_PROJECT_ID_HERE",
  storageBucket: "YOUR_STORAGE_BUCKET_HERE",
  messagingSenderId: "YOUR_MESSAGING_SENDER_ID_HERE",
  appId: "YOUR_APP_ID_HERE",
  measurementId: "YOUR_MEASUREMENT_ID_HERE",
  apiKey: "YOUR_API_KEY_HERE",
  authDomain: "YOUR_AUTH_DOMAIN_HERE",
  projectId: "YOUR_PROJECT_ID_HERE",
  storageBucket: "YOUR_STORAGE_BUCKET_HERE",
  messagingSenderId: "YOUR_MESSAGING_SENDER_ID_HERE",
  appId: "YOUR_APP_ID_HERE",
  measurementId: "YOUR_MEASUREMENT_ID_HERE",
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);
const messaging = getMessaging(app);

document.addEventListener("alpine:init", () => {
  Alpine.store("fcm", {
    notificationPermission: Notification.permission === "granted",
    $dispatch(name, detail) {
      document.dispatchEvent(new CustomEvent(name, detail));
    },
    getOS() {
      const userAgent = window.navigator.userAgent;

      let os = "Unknown Os";

      if (userAgent.indexOf("Win") !== -1) os = "Windows";
      if (userAgent.indexOf("Mac") !== -1) os = "macOS";
      if (userAgent.indexOf("Linux") !== -1) os = "Linux";
      if (userAgent.indexOf("Android") !== -1) os = "Android";
      if (userAgent.indexOf("like Mac") !== -1) os = "iOS";

      return os + "-" + Math.random().toString(36).substring(2);
    },
    checkForTokenChange(callback) {
      onTokenChanged(app, (token) => {
        callback({ token: currentToken, os: this.getOS() });
      });
    },
    getFCMToken(callback) {
      getToken(messaging, {
        vapidKey:
          "BKwVTYMpu_Ob2FeMvGYCr44-n7ZbZBfP7-sAdKRKXzDrOmi1rMGZcbnmu9eEObKeE_oZ7hOJ0IBHh1WSpORUW0c",
      }) // Optional: add VAPID key here if required
        .then((currentToken) => {
          if (currentToken) {
            callback({ token: currentToken, os: this.getOS() });
          } else {
            console.log(
              "No registration token available. Request permission to generate one."
            );
          }
        })
        .catch(function (err) {
          console.log(err);
        });
    },
    getPermission(callback) {
      Notification.requestPermission().then((permission) => {
        if (permission === "granted") {
          console.log("Notification permission granted.");
          this.getFCMToken(callback);
          this.notificationPermission = true;
        } else {
          console.log("Unable to get permission to notify.");
          this.notificationPermission = false;
        }
      });
    },
  });
});

onMessage(messaging, (payload) => {
  console.log("Message received in the foreground: ", payload);

  // Customize notification
  const notificationTitle = payload.notification.title;
  const notificationOptions = {
    body: payload.notification.body,
    icon: "/your-icon.png",
  };

  // Display notification
  new Notification(notificationTitle, notificationOptions);
});
