window.addEventListener('load', function() {
    const { __, _x, _n, sprintf } = wp.i18n;

    const context = window['sybat'];
    const nonce = context.nonce;
    axios.defaults.headers.common['X-WP-Nonce'] = nonce;
    const { createApp, ref } = Vue;

    createApp({
        setup() {
            const adminUrl = ref(context.adminUrl);
            const message = ref(null);
            const posts = ref(null);
            const checkedPosts = ref([]);
            const processingSearch = ref(false);
            const processingUpdate = ref(false);

            function searchPosts() {
                message.value = null;
                processingSearch.value = true;
                checkedPosts.value = [];
                axios.get("/wp-json/sybat/search-posts")
                    .then((response) => {
                        posts.value = response.data;
                        response.data.forEach((post) => {
                            if (post.editable && post.tag_required) {
                                checkedPosts.value.push(post.id);
                            }
                        });
                    })
                    .catch((reason) => {
                        alert(reason);
                    })
                    .finally(() => {
                        processingSearch.value = false;
                    });

            }

            function updateTag(event) {
                message.value = null;
                processingUpdate.value = true;
                axios.post("/wp-json/sybat/update-post-tags", {id: checkedPosts.value})
                    .then((response) => {
                        if (checkedPosts.value.length == 1) {
                            message.value = 
                                __('Updated tags for 1 post.', 'syb-ad-tagging');
                        } else {
                            message.value = 
                                sprintf(__('Updated tags for %d posts.', 'syb-ad-tagging'), checkedPosts.value.length);
                        }
                        posts.value = null;
                    })
                    .catch((reason) => {
                        alert(reason);
                    })
                    .finally(() => {
                        processingUpdate.value = false;
                    });
            }

            function openSetting() {
                location = adminUrl.value + 'options-general.php?page=sybat';
            }
    
            return {
                adminUrl,
                message,
                posts,
                checkedPosts,
                processingSearch,
                processingUpdate,
                searchPosts,
                updateTag,
                openSetting,
            }
        }
    }).mount('#sybat-app');
});

