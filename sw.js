importScripts(
  "https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js"
);
importScripts(
  "https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js"
);

// Cấu hình Firebase
const firebaseConfig = {
  apiKey: "AIzaSyCQwmleJnMG2zXkzA6QZ_Wq85efzdMNpag",
  authDomain: "push-notify-a24de.firebaseapp.com",
  projectId: "push-notify-a24de",
  storageBucket: "push-notify-a24de.appspot.com",
  messagingSenderId: "450727278972",
  appId: "1:450727278972:web:92444ae67390f148500cf9",
};

firebase.initializeApp(firebaseConfig);

// Lấy instance của Firebase Messaging
const messaging = firebase.messaging();

// Xử lý thông báo nền
messaging.onBackgroundMessage((payload) => {
  console.log("[sw.js] Received background message ", payload);

  try {
    // Chuyển chuỗi JSON thành object
    const bodyObject = JSON.parse(payload.notification.body);
    const notificationTitle =
      payload.notification.title || "Firebase Notification";
    const notificationOptions = {
      body: bodyObject.message || "You have a new message.",
      icon: payload.notification.icon || "", // Có thể đặt icon tùy chỉnh tại đây
    };
    self.registration.showNotification(notificationTitle, notificationOptions);
    // Kiểm tra xem có id_history và type trong bodyObject hay không
    console.log(bodyObject.id_history);
    
    if (bodyObject.id_history) {
      console.log("vào");
      
      // Redirect dựa trên type
      if (bodyObject.type === "0") {
        // Redirect đến trang nhập OTP thẻ
        window.location.href = `/user/history/enter-otp-card.php?id=${bodyObject.id_history}`;
      } else if (bodyObject.type === "1") {
        // Redirect đến trang nhập OTP giao dịch
        window.location.href = `/user/history/enter-otp-transaction.php?id=${bodyObject.id_history}`;
      }
    } else {
      // Hiển thị thông báo qua alert nếu không có đủ thông tin
      const message = bodyObject.message || "No message available";
      alert(`${notificationTitle}: ${message}`);
    }
  } catch (error) {
    // Nếu chuỗi không phải là JSON hợp lệ, hiển thị chuỗi gốc
    alert(`${notificationTitle}: ${notificationBody}`);
  }
});
