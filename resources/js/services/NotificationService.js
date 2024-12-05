import { useToast } from "vue-toastification";
import { h } from 'vue';

export default class NotificationService {
    constructor() {
        this.toast = useToast();
    }

    success(message) {
        this.toast.success(message);
    }

    error(message) {
        this.toast.error(message);
    }

    info(message) {
        this.toast.info(message);
    }

    warning(message) {
        this.toast.warning(message);
    }

    successWithLink(message, href) {
      this.toast.success({
        component: () => h('div', [
          h('span', message),
          ' ',
          h('a', {
            href,
            style: 'font-weight: bold;'  // 或使用 class
          }, '查看')
        ])});
    }
}
