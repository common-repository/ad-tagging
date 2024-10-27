window.addEventListener('load', function() {
    const { __, _x, _n, sprintf } = wp.i18n;

    const context = window['sybat'];
    const nonce = context.nonce;
    axios.defaults.headers.common['X-WP-Nonce'] = nonce;
    const { createApp, ref } = Vue;

    createApp({
        setup() {
            const message = ref(null);
            const adTagName = ref(context.adTagName);
            const affiliateUrls = ref(context.affiliateUrlList.join('\r\n'));
            const updateOnSave = ref(context.updateOnSave);

            function validateForm() {
                if (affiliateUrls.value.trim() == '') {
                    return false;
                }
                return true;
            }

            function updateSettings(event) {
                message.value = null;
                axios.post("/wp-json/sybat/update-settings", {
                    'ad_tag_name': adTagName.value,
                    'affiliate_urls': affiliateUrls.value,
                    'update_on_save': updateOnSave.value,
                    })
                    .then((response) => {
                        message.value = __('Changes saved.', 'syb-ad-tagging');
                    })
                    .catch((reason) => {alert(reason);})
                    .finally(() => {});
            }

            return {
                message,
                adTagName,
                affiliateUrls,
                updateOnSave,
                validateForm,
                updateSettings,
            }
        }
    }).mount('#sybat-app');

});

