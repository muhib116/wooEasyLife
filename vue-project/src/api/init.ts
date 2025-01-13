export let baseUrl = ''
if (location.hostname == 'localhost') {
    baseUrl = import.meta.env.DEV ? 'http://localhost:8080/wordpress' : location.origin + '/wordpress'
} else {
    baseUrl = location.origin
}

export const localApiBaseURL = `${baseUrl}/wp-json/wooeasylife/v1`