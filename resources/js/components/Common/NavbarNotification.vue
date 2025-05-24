<template>
    <div class="position-relative">
        <span
            v-if="unreadCount > 0"
            class="notification-badge position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
        >
            {{ unreadCount }}
        </span>
        <svg
            type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#offcanvasRight"
            aria-controls="offcanvasRight"
            @click="markAllAsRead"
            width="18"
            height="20"
            viewBox="0 0 18 20"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path
                d="M1.10557 14.5624L0.275584 14.0047L0.275584 14.0047L1.10557 14.5624ZM2.26855 10.835L1.26855 10.8242V10.835H2.26855ZM2.29296 8.5814L3.29296 8.59223V8.5814H2.29296ZM16.9006 14.5805L17.7412 14.0388L17.7412 14.0388L16.9006 14.5805ZM15.7814 10.835L14.7814 10.8236V10.835H15.7814ZM15.8058 8.69307L16.8058 8.70447V8.69307H15.8058ZM6.33333 18C5.78105 18 5.33333 18.4477 5.33333 19C5.33333 19.5523 5.78105 20 6.33333 20V18ZM11.6667 20C12.219 20 12.6667 19.5523 12.6667 19C12.6667 18.4477 12.219 18 11.6667 18V20ZM1.93556 15.1202C2.60782 14.1199 3.26855 12.6372 3.26855 10.835H1.26855C1.26855 12.1434 0.786904 13.2438 0.275584 14.0047L1.93556 15.1202ZM3.26849 10.8459L3.2929 8.59223L1.29302 8.57057L1.26861 10.8242L3.26849 10.8459ZM17.7412 14.0388C17.2508 13.2779 16.7814 12.1648 16.7814 10.835H14.7814C14.7814 12.6372 15.415 14.1213 16.0601 15.1222L17.7412 14.0388ZM16.7814 10.8464L16.8058 8.70447L14.8059 8.68168L14.7815 10.8236L16.7814 10.8464ZM16.8058 8.69307C16.8058 4.01072 13.4227 0 9 0V2C12.0948 2 14.8058 4.8779 14.8058 8.69307H16.8058ZM16.4933 16.625C17.2957 16.625 17.7284 16.0028 17.8809 15.5978C18.042 15.1701 18.0779 14.5612 17.7412 14.0388L16.0601 15.1222C16.0102 15.0448 16.0016 14.9807 16.0003 14.9546C15.9989 14.9285 16.0027 14.9102 16.0092 14.893C16.0147 14.8785 16.0364 14.8268 16.1034 14.7673C16.1782 14.7009 16.314 14.625 16.4933 14.625V16.625ZM3.29296 8.5814C3.29296 4.8279 5.95974 2 9 2V0C4.63187 0 1.29296 3.96072 1.29296 8.5814H3.29296ZM1.50763 14.625C1.69077 14.625 1.82791 14.7038 1.90175 14.7706C1.96767 14.8302 1.98774 14.8808 1.99217 14.8929C1.99767 14.908 2.00119 14.9244 1.99964 14.9498C1.9981 14.9749 1.98888 15.0409 1.93556 15.1202L0.275584 14.0047C-0.077099 14.5295 -0.043574 15.1489 0.11355 15.5791C0.261638 15.9845 0.692398 16.625 1.50763 16.625V14.625ZM16.4933 14.625H1.50763V16.625H16.4933V14.625ZM6.33333 20H11.6667V18H6.33333V20Z"
                fill="#252C32"
            />
        </svg>
    </div>
    <div
        class="offcanvas offcanvas-end navbar-offcanvas"
        tabindex="-1"
        id="offcanvasRight"
        aria-labelledby="offcanvasRightLabel"
    >
        <div class="offcanvas-header">
            <h5 id="offcanvasRightLabel" class="text-uppercase">
                Notifications
            </h5>
            <button
                type="button"
                class="btn-close text-reset"
                data-bs-dismiss="offcanvas"
                aria-label="Close"
            ></button>
        </div>
        <div class="offcanvas-body">
            <div
                class="d-flex align-items-center mb-3"
                v-for="notification in notifications"
                :key="notification.uuid"
                :class="{
                    'notification-read': ['approved', 'declined'].includes(
                        notification.data?.status
                    ),
                }"
            >
                <img
                    style="
                        width: 24px;
                        height: 24px;
                        object-fit: cover;
                        border-radius: 8px;
                    "
                    src="assets/images/nav-default-img.svg"
                />
                <div
                    style="margin-left: 8px"
                    @click="handleNotificationClick(notification)"
                >
                    <p class="notification-list">
                        {{
                            notification.data?.message ?? "Notification message"
                        }}
                    </p>
                    <span class="notification-time">
                        {{
                            moment(notification.created_at).format(
                                "dddd, MMMM Do, YYYY, h:mm:ss A"
                            )
                        }}
                    </span>
                </div>
            </div>
            <p v-if="notifications.length === 0">No notifications available.</p>
        </div>
    </div>
    <audio id="notification-sound" src="../../assets/notification.mp3"></audio>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import moment from "moment";
import { useLeavStore } from "../../stores/leaveStore";
import { useUserStore } from "../../stores/userStore";
import axios from "axios";

const leaveStore = useLeavStore();
const userStore = useUserStore();

const props = defineProps({
    notifications: {
        type: Array,
        default: [],
    },
    authuser: {
        type: Object,
        default: {},
    },
});

const notifications = ref([]);

// Compute unread notifications count
const unreadCount = computed(() => {
    return notifications.value.filter((n) => !n.read_at).length;
});

// Handle click on a notification and mark it as read

const handleNotificationClick = async (notification) => {
    if (notification?.data?.leave_id) {
        leaveStore.setLeave(notification?.data);
        leaveStore.setLeaveStatus(
            notification?.data?.status === "negotiated"
                ? "re-negotiated"
                : notification?.data?.status === "pending"
                ? "approved"
                : notification?.data?.status
        );
        $("#apply-leave-popup").modal("show");
    }
};

// Mark all notifications as read when opening the notification list
const markAllAsRead = async () => {
    try {
        await axios.post("/notifications/mark-all-read");

        notifications.value.forEach((n) => {
            if (!n.read_at) n.read_at = new Date();
        });
    } catch (error) {
        console.error("Error marking all notifications as read:", error);
    }
};

onMounted(() => {
    notifications.value = props.notifications;
    userStore.setAuthUser(props?.authuser);
    listenToNotificationChannel();
});

const listenToNotificationChannel = () => {
    window.Echo.private("tenant-leave-channel." + currentAuth.authUuid)
        .listen(".leaveNotification", (e) => {
            notifications.value = [
                { data: e.leaveResponse },
                ...notifications.value,
            ];
            const sound = document.getElementById("notification-sound");
            if (sound) sound.play();
        })
        .on("pusher:subscription_error", (error) => {
            console.log(
                `pusher:subscription_error in NavbarNotification component: ${error}`
            );
        })
        .on("leave", (s) => {});

    window.Echo.private("project-chat-channel." + currentAuth.authUuid).listen(
        ".project-private-chat-message",
        (e) => {
            const senderId = e?.senderId ?? '';

            // Prevent showing notification to sender
            if (senderId === currentAuth.authUuid) return;

            notifications.value = [
                { data: e.payload, created_at: new Date() },
                ...notifications.value,
            ];

            const sound = document.getElementById("notification-sound");
            if (sound) sound.play();
        }
    );
};
</script>

<style scoped>
.notification-badge {
    font-size: 0.75rem;
    padding: 0.25em 0.5em;
    z-index: 1;
}
.notification-read {
    background-color: #f1f1f1; /* Light grey background for visual distinction */
    color: #333;
}
</style>
