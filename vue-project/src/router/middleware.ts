import { useLicense } from '@/pages/config/license/UseLicense'


export default function (router) 
{
    const {
        licenseKey,
        isValidLicenseKey,
        loadLicenseKey
    } = useLicense(false)

    router.beforeEach(async (to, from, next) => 
    {
        if(!licenseKey.value) {
            await loadLicenseKey()
        }

        // Skip validation for the license page
        if (to.name === 'license') {
            return next();
        }


        
        if (!isValidLicenseKey.value) {
            // Redirect to the license route if the key is invalid or missing
            return next({ name: 'license' });
        }

        // Proceed to the next route
        next();
    });

    return router;
}
