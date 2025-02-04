import { useLicense } from "@/pages/config/license/UseLicense";
import {
  licenseKey,
  isValidLicenseKey
} from '@/service/useServiceProvider'

export default function (router) {
  const { loadLicenseKey } = useLicense(false);

  router.beforeEach(async (to, from, next) => {
    try {
      if (!licenseKey.value) {
        await loadLicenseKey();
      }

      // Allow access to the license route without validation
      if (to.name === "license") {
        return next();
      }

      // Redirect to the license route if the key is invalid or missing
      if (!isValidLicenseKey.value) {
        return next({ name: "license" });
      }

      // Proceed to the intended route
      next();
    } catch (error) {
      console.error("Error validating license:", error);
      // Redirect to the license page in case of an error
      next({ name: "license" });
    }
  });

  return router;
}
