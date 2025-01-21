import { useLicense } from '@/pages/config/license/UseLicense';

export default function (router) {
    const {
        licenseKey,
        isValidLicenseKey,
        loadLicenseKey
    } = useLicense(false);

    router.beforeEach(async (to, from, next) => {
        // Skip validation for the license route to prevent infinite redirects
        if (to.name === 'license') {
            return next();
        }

        // Load the license key if not already loaded
        if (!licenseKey.value) {
            await loadLicenseKey();
        }

        // Redirect if the license key is invalid
        if (!isValidLicenseKey.value) {
            return next({ name: 'license' });
        }

        // Proceed to the intended route
        next();
    });

    return router;
}
