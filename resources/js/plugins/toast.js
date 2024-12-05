import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";

const options = {
    position: "bottom-right",
    timeout: 3000,
    closeOnClick: true,
    pauseOnHover: true,
    draggable: true,
    toastClassName: "bg-white shadow-lg rounded-lg",
    bodyClassName: "text-sm font-medium text-gray-900",
    progressClassName: "bg-green-500",
};

export { Toast, options };
